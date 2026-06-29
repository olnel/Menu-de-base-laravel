<?php

namespace App\Services;

use App\Models\ArticleTransaction;
use App\Repositories\ArticleTransactionServiceRepository;
use App\Repositories\VehiculeRepository;
use App\Services\Base\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleTransactionService extends BaseService
{
    protected $repository;
    protected VehiculeRepository $vehiculeRepository;
    protected ArticleTransactionDetailService $articleTransactionDetailService;
    protected ArticleMouvementService $articleMouvementService;

    protected array $relation = ['magasin', 'magasincible', 'vehicule', 'user'];

    protected array $scope = [
        'filter'          => 'search',
        'magasin'         => 'magasin_id',
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
    ];

    const MSG_INSERT_SUCCESS = 'INSERTION TERMINÉE AVEC SUCCÈS';
    const MSG_UPDATE_SUCCESS = 'MODIFICATION TERMINÉE AVEC SUCCÈS';
    const MSG_DELETE_SUCCESS = 'SUPPRESSION TERMINÉE AVEC SUCCÈS';
    const MSG_ERROR_INSERT = 'ERREUR LORS DE L\'INSERTION';
    const MSG_ERROR_UPDATE = 'ERREUR LORS DE LA MODIFICATION';
    const MSG_ERROR_DELETE = 'ERREUR LORS DE LA SUPPRESSION';

    public function __construct(
        ArticleTransactionServiceRepository $articleTransactionServiceRepository,
        VehiculeRepository $vehiculeRepository,
        ArticleTransactionDetailService $articleTransactionDetailService,
        ArticleMouvementService $articleMouvementService
    ) {
        $this->repository = $articleTransactionServiceRepository;
        $this->vehiculeRepository = $vehiculeRepository;
        $this->articleTransactionDetailService = $articleTransactionDetailService;
        $this->articleMouvementService = $articleMouvementService;
        parent::__construct($articleTransactionServiceRepository);
    }

    /**
     * Génère le numéro de commande en fonction du mois courant
     */
    private function getNumeroCommande(): int
    {
        $first_date = Carbon::now()->startOfMonth()->toDateString();
        $last_date = Carbon::now()->endOfMonth()->toDateString();

        $lastCommande = $this->repository->find($first_date, $last_date);

        return $lastCommande ? $lastCommande->count_mouvement + 1 : 1;
    }

    /**
     * Génère le numéro formaté du bon de commande
     */
    public function generateReference(): array
    {
        $count = $this->getNumeroCommande();
        $moisAnnee = Carbon::now()->format('m-Y');
        $numero = sprintf('AREF-%03d/%s', $count, $moisAnnee);

        return [
            'count_mouvement' => $count,
            'reference_mouvement' => $numero
        ];
    }

    public function saveWithDetails(mixed $validated): array
    {
        DB::beginTransaction();

        try {
            $details = $validated['details'];
            $dateHeure = now();

            // Step 1: Verify vehicle
            $validated = $this->processVehicle($validated);

            // Step 2: Generate reference
            $reference = $this->generateReference();
            $preparedData = $this->prepareData($validated, $dateHeure, $reference);

            // Step 3: Save main transaction
            unset($validated['details']);
            $mouvement = $this->saveMouvement($preparedData);

            // Step 4: Save details
            $this->saveDetails($mouvement['element']->id, $details, $dateHeure, $preparedData, $validated['magasin_cible']);

            DB::commit();
            return $this->successResponse(self::MSG_INSERT_SUCCESS, $mouvement);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in saveWithDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            return $this->errorResponse(self::MSG_ERROR_INSERT, $e);
        }
    }

    public function editWithDetails(ArticleTransaction $article_mouvement, mixed $validated): array
    {
        DB::beginTransaction();

        try {
            // Step 1: Annulation des mouvements existants
            $dateHeure = now();
            $this->cancelExistingMovements($article_mouvement, $dateHeure);

            // Step 2: Suppression des détails existants
            $this->articleTransactionDetailService->deleteByIDTransaction($article_mouvement->id);

            // Step 3: Mise à jour avec les nouvelles données
            $details = $validated['details'];


            // Vérification véhicule
            $validated = $this->processVehicle($validated);

            // Préparation des données
            $reference = [
                'reference_mouvement' => $article_mouvement->reference_mouvement,
                'count_mouvement' => $article_mouvement->count_mouvement
            ];
            $preparedData = $this->prepareData($validated, $dateHeure, $reference);

            // Mise à jour de la transaction principale
            unset($validated['details']);
            $mouvement = $this->editMouvement($preparedData, $article_mouvement);

            // Sauvegarde des nouveaux détails
            $this->saveDetails($mouvement['element']->id, $details, $dateHeure, $preparedData, $validated['magasin_cible']);

            DB::commit();
            return $this->successResponse(self::MSG_UPDATE_SUCCESS, $mouvement);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in editWithDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            return $this->errorResponse(self::MSG_ERROR_UPDATE, $e);
        }
    }

    public function deleteWithDetails(ArticleTransaction $article_mouvement): array
    {
        DB::beginTransaction();

        try {
            // Annulation des mouvements existants
            $date_heure_enregistrement = now();
            $this->cancelExistingMovements($article_mouvement, $date_heure_enregistrement);

            // Suppression des détails
            $this->articleTransactionDetailService->deleteByIDTransaction($article_mouvement->id);

            // Suppression de la transaction principale
            $result = parent::delete($article_mouvement);

            DB::commit();
            return $this->successResponse(self::MSG_DELETE_SUCCESS, $result);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in deleteWithDetails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $article_mouvement
            ]);
            return $this->errorResponse(self::MSG_ERROR_DELETE, $e);
        }
    }

    private function cancelExistingMovements(ArticleTransaction $article_mouvement, $date_heure_enregistrement): void
    {
        $type_mvt = $article_mouvement->type_mvt;
        $details = $article_mouvement->details;

        $data_mvt = [
            'date_mvt' => date('Y-m-d'),
            'date_heure_enregistrement' => $date_heure_enregistrement,
            'reference_mvt' => 'ANNULLATION '.$article_mouvement->reference_mouvement,
            'user_id' => Auth::id()
        ];

        foreach ($details as $detail) {
            $data_mvt['article_id'] = $detail->article_id;
            $data_mvt['qte_mvt'] = $detail->qte_mouvement;

            if ($type_mvt === 'Transfert') {
                $data_mvt['magasin_id'] = $article_mouvement->magasin_cible;
                $data_mvt['operation_mvt'] = 'ANNULATION TRANSFERT(Sortie)';
                $this->articleMouvementService->sortieMouvement($data_mvt);

                $data_mvt['magasin_id'] = $article_mouvement->magasin_id;
                $data_mvt['operation_mvt'] = 'ANNULATION TRANSFERT(Entrée)';
                $this->articleMouvementService->entrerMouvement($data_mvt);
            } elseif ($type_mvt === 'Entrée') {
                $data_mvt['magasin_id'] = $article_mouvement->magasin_id;
                $data_mvt['operation_mvt'] = 'ANNULATION MOUVEMENT Entrée';
                $this->articleMouvementService->sortieMouvement($data_mvt);
            } elseif ($type_mvt === 'Sortie') {
                $data_mvt['magasin_id'] = $article_mouvement->magasin_id;
                $data_mvt['operation_mvt'] = 'ANNULATION MOUVEMENT Sortie';
                $this->articleMouvementService->entrerMouvement($data_mvt);
            }
        }
    }

    private function processVehicle(mixed $validated): mixed
    {
        if (!is_null($validated['vehicule_id'])) {
            $critere_vehicule = ['immatriculation' => $validated['vehicule_id']];
            $vehicule = $this->vehiculeRepository->findElement($critere_vehicule);
            if (!$vehicule) {
                throw new \RuntimeException('Vehicle not found');
            }
            $validated['vehicule_id'] = $vehicule->id;
        }
        return $validated;
    }

    private function prepareData(mixed $validated, \Illuminate\Support\Carbon $dateHeure, array $numero): array
    {
        return [
            ...$validated,
            'date_heure_enregistrement' => $dateHeure,
            'reference_mouvement' => $numero['reference_mouvement'],
            'count_mouvement' => $numero['count_mouvement'],
            'user_id' => Auth::id(),
        ];
    }

    private function saveMouvement(array $preparedData): array
    {
        return parent::create($preparedData);
    }

    private function editMouvement(array $preparedData, ArticleTransaction $article_mouvement): array
    {
        return parent::update($article_mouvement, $preparedData);
    }

    private function saveDetails($id, mixed $details, $dateHeure, $preparedData, $magasin_cible): void
    {
        foreach ($details as $detail) {
            $detail['article_transaction_id'] = $id;
            $detail['magasin_id'] = $preparedData['magasin_id'];
            $this->articleTransactionDetailService->create($detail);

            $data_mvt = [
                'article_id' => $detail['article_id'],
                'qte_mvt' => $detail['qte_mouvement'],
                'date_heure_enregistrement' => $dateHeure,
                'operation_mvt' => 'MOUVEMENT '.$preparedData['type_mvt'],
                'reference_mvt' => $preparedData['reference_mouvement'],
                'date_mvt' => $preparedData['date_transaction'],
                'magasin_id' => $detail['magasin_id'],
                'user_id' => $preparedData['user_id'],
            ];

            if ($preparedData['type_mvt'] === 'Entrée') {
                $this->articleMouvementService->entrerMouvement($data_mvt);
            } elseif ($preparedData['type_mvt'] === 'Sortie') {
                $this->articleMouvementService->sortieMouvement($data_mvt);
            } elseif ($preparedData['type_mvt'] === 'Transfert') {
                $data_mvt_cible = [
                    ...$data_mvt,
                    'magasin_id' => $magasin_cible,
                    'operation_mvt' => 'TRANSFERT (Entrée)',
                    'reference_mvt' => 'RECEPTION '.$preparedData['reference_mouvement']
                ];
                $this->articleMouvementService->entrerMouvement($data_mvt_cible);

                $data_source = [
                    ...$data_mvt,
                    'operation_mvt' => 'TRANSFERT (Sortie)',
                    'reference_mvt' => 'TRANSFERT '.$preparedData['reference_mouvement']
                ];
                $this->articleMouvementService->sortieMouvement($data_source);
            }
        }
    }
}
