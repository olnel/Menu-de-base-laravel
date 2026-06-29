<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\Notification;
use App\Models\Salarie;
use App\Models\SessionFormation;
use App\Models\SessionFormationParticipant;
use App\Repositories\FormationRepository;
use App\Services\Base\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormationService extends BaseService
{
    protected array $relation = ['formationSuivante'];
    protected array $scope = ['filter' => 'search'];

    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('nom')->setFilterValue('id');
    }

    /**
     * Surcharge pour ajouter le withCount des sessions de formation
     */
    public function getAll(array $filters = [])
    {
        $relation = $this->relation;
        $scope = $this->scope;

        $customQuery = function ($query) {
            $query->withCount('sessionFormations');
        };

        return $this->repository->fetchData($relation, $filters, $scope, $this->filterValue, $this->filterLabel, $customQuery);
    }

    /**
     * Crée une formation
     */
    public function createFormation(array $validated): array
    {
        DB::beginTransaction();
        try {
            $formation = $this->repository->create($validated);

            // 🔁 Créer notification pour la formation suivante si elle existe
            if ($formation->formation_suivante_id) {
                $this->notifierFormationSuivante($formation);
            }

            DB::commit();
            return $this->successResponse('Formation enregistrée avec succès', $formation);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création formation: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }
    }

    /**
     * Met à jour une formation
     */
    public function updateFormation(Formation $formation, array $validated): array
    {
        DB::beginTransaction();
        try {
            $this->repository->update($formation, $validated);
            DB::commit();
            return $this->successResponse('Formation mise à jour avec succès', $formation);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la mise à jour', $e);
        }
    }

    /**
     * Supprime une formation
     */
    public function deleteFormation(Formation $formation): array
    {
        DB::beginTransaction();
        try {
            // Supprimer les notifications liées aux sessions de cette formation
            $sessionIds = $formation->sessionFormations()->pluck('id');
            Notification::where('notifiable_type', SessionFormation::class)
                ->whereIn('notifiable_id', $sessionIds)
                ->delete();

            $this->repository->delete($formation);
            DB::commit();
            return $this->successResponse('Formation supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la suppression', $e);
        }
    }

    /**
     * Crée une session de formation avec ses participants
     */
    public function createSession(array $validated): array
    {
        DB::beginTransaction();
        try {
            $formation = Formation::findOrFail($validated['formation_id']);

            // Calculer la date de prochaine formation
            $dateFormation = Carbon::parse($validated['date_formation']);
            $prochaineDate = $dateFormation->copy()->addMonths($formation->periode_renouvellement_mois);

            // Créer la session
            $session = SessionFormation::create([
                'formation_id' => $validated['formation_id'],
                'date_formation' => $validated['date_formation'],
                'date_prochaine_formation' => $prochaineDate,
                'observation' => $validated['observation'] ?? null,
            ]);

            // Ajouter les participants
            if (!empty($validated['salarie_ids'])) {
                foreach ($validated['salarie_ids'] as $salarieId) {
                    SessionFormationParticipant::create([
                        'session_formation_id' => $session->id,
                        'salarie_id' => $salarieId,
                    ]);
                }
            }

            // 🔁 Si cette formation a une formation suivante → créer une notification
            if ($formation->formation_suivante_id) {
                $this->notifierFormationSuivanteAvecParticipants(
                    $formation,
                    $session,
                    $validated['salarie_ids'] ?? []
                );
            }

            DB::commit();

            $session->load(['formation', 'participants.salarie']);

            return $this->successResponse('Session de formation enregistrée avec succès', $session);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création session formation: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }
    }

    /**
     * Récupère les participants de la dernière session d'une formation
     */
    public function getParticipantsDerniereSession(int $formationId): array
    {
        $formation = Formation::with('sessionFormations.participants.salarie')->findOrFail($formationId);
        $derniereSession = $formation->sessionFormations()
            ->latest('date_formation')
            ->with('participants.salarie')
            ->first();

        if (!$derniereSession) {
            return [
                'formation' => $formation,
                'session' => null,
                'participants' => [],
            ];
        }

        return [
            'formation' => $formation,
            'session' => $derniereSession,
            'participants' => $derniereSession->participants->map(function ($p) {
                return $p->salarie;
            }),
        ];
    }

    /**
     * Notifie qu'une formation a une formation suivante à planifier
     */
    private function notifierFormationSuivante(Formation $formation): void
    {
        $suivante = $formation->formationSuivante;
        if (!$suivante) return;

        Notification::updateOrCreate(
            [
                'notifiable_type' => Formation::class,
                'notifiable_id' => $formation->id,
            ],
            [
                'type' => 'formation_chainee',
                'titre' => $suivante->nom,
                'message' => "La formation « {$formation->nom} » a une formation suivante « {$suivante->nom} » à planifier.",
                'jours_restants' => null,
                'data' => [
                    'formation_id' => $formation->id,
                    'formation_nom' => $formation->nom,
                    'formation_suivante_id' => $suivante->id,
                    'formation_suivante_nom' => $suivante->nom,
                ],
            ]
        );
    }

    /**
     * Notifie avec les participants pré-sélectionnés pour la formation suivante
     */
    private function notifierFormationSuivanteAvecParticipants(
        Formation $formation,
        SessionFormation $session,
        array $salarieIds
    ): void {
        $suivante = $formation->formationSuivante;
        if (!$suivante) return;

        $salaries = Salarie::whereIn('id', $salarieIds)->get(['id', 'nom', 'prenom']);

        Notification::updateOrCreate(
            [
                'notifiable_type' => SessionFormation::class,
                'notifiable_id' => $session->id,
            ],
            [
                'type' => 'formation_suivante',
                'titre' => "{$suivante->nom} (suite de {$formation->nom})",
                'message' => "Les participants de « {$formation->nom} » doivent être planifiés pour « {$suivante->nom} ».",
                'jours_restants' => null,
                'data' => [
                    'type' => 'formation_chainee',
                    'formation_origine_id' => $formation->id,
                    'formation_origine_nom' => $formation->nom,
                    'formation_suivante_id' => $suivante->id,
                    'formation_suivante_nom' => $suivante->nom,
                    'session_origine_id' => $session->id,
                    'participants' => $salaries->map(fn ($s) => [
                        'id' => $s->id,
                        'nom' => $s->nom,
                        'prenom' => $s->prenom,
                    ]),
                ],
            ]
        );
    }
}
