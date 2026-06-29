<?php

namespace App\Services;

use App\Models\PneuSerie;
use App\Models\Vehicule;
use App\Repositories\RemorqueRepository;
use App\Repositories\VehiculeRepository;
use App\Services\Base\BaseService;
use App\Services\DocumentDynamicService;
use App\Services\PlanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehiculeService extends BaseService
{
    protected $vehiculeElementService;
    protected $repository;
    protected $vehiculePhotoService, $vehiculedocumentService;
    protected DocumentDynamicService $documentDynamicService;
    protected array $relation = ['element_vehicules.pneuSerie', 'chauffeurs', 'remorque', 'documents', 'documents.documentType'];

    protected array $scope = [
        'filter' => 'search',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date',
        'filterchauffeur' => 'chauffeur_id',
        'filterclient' => 'client_id',
    ];

    public function __construct(
        VehiculeRepository $vehiculeRepository,
        VehiculeElementService $vehiculeElementService,
        VehiculePhotoService $vehiculePhotoService,
        VehiculeDocumentService $vehiculeDocumentService,
        protected readonly RemorqueRepository $remorqueRepository,
        protected readonly RemorqueService $remorqueService,
        protected readonly PneuSerieService $pneuSerieService,
        protected readonly PneuMouvementService $pneuMouvementService,
        DocumentDynamicService $documentDynamicService
    ) {
        $this->repository = $vehiculeRepository;
        $this->vehiculeElementService = $vehiculeElementService;
        $this->vehiculePhotoService = $vehiculePhotoService;
        $this->vehiculedocumentService = $vehiculeDocumentService;
        $this->documentDynamicService = $documentDynamicService;

        parent::__construct($vehiculeRepository);
    }



    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('immatriculation');
    }

    private function extractVehicule(array $validated): array
    {
        $vehicule = [
            'param_element_id' => $validated['param_element_id'],
            'immatriculation' => $validated['immatriculation'],
            'marque' => $validated['marque'],
            'modele' => $validated['modele'],
            'remorque_id' => $validated['remorque_id'],
            'num_chassis' => $validated['num_chassis'],
            'couleur' => $validated['couleur'],
            'num_carte_grise' => $validated['num_carte_grise'],
            'nbre_porte' => $validated['nbre_porte'] ?? 0,
            'valeur_initial' => $validated['valeur_initial'] ?? 0,
        ];
        return $vehicule;
    }

    /**
     * Crée un nouvel élément avec ses détails
     *
     * @param array $validated donnée validées
     * @return array ['error' => bool, 'message' => string]
     */
    public function create(array $validated): array
    {
        $maxVehicules = PlanService::getMaxVehicules();

        if ($maxVehicules !== null && Vehicule::count() >= $maxVehicules) {
            return ['error' => true, 'message' => "Votre forfait ne permet pas d'ajouter plus de {$maxVehicules} véhicule(s). Veuillez upgrader votre abonnement."];
        }

        DB::beginTransaction();
        try {
            // Extraction des documents
            $documents = $validated['documents'] ?? [];
            unset($validated['documents']);

            $data_vehicule = $this->extractVehicule($validated);
            //1- Création de l'élément principale
            $element = $this->repository->create($data_vehicule);
            $id = $element->id;

            // 2 - Création des détails associés s'ils existent
            if (!empty($validated['element_vehicules'])) {
                $this->createDetails($id, $validated['element_vehicules']);
                // Assigner les séries de pneus et créer un mouvement si nécessaire
                foreach ($validated['element_vehicules'] as $detail) {
                    $numeroSerie = $detail['numero_serie'] ?? null;
                    if (!empty($numeroSerie) && ($detail['is_pneu'] ?? false)) {
                        $serie = $this->pneuSerieService->getByNumeroSerie($numeroSerie);
                        if ($serie) {
                            if (!empty($detail['date_montage'])) {
                                $serie->date_montage = $detail['date_montage'];
                            }
                            if (!empty($detail['etat_piece'])) {
                                $serie->etat = $detail['etat_piece'];
                            }
                            $emplacement = $detail['emplacement'] ?? null;
                            if (!empty($emplacement)) {
                                if ($detail['is_first'] ?? false) {
                                    $serie->position_initial = $emplacement;
                                }
                                $serie->position_actuel = $emplacement;
                            }
                            $serie->is_first = boolval($detail['is_first'] ?? false);
                            $serie->save();
                            $this->pneuSerieService->assignToVehiculeByNumeroSerie($numeroSerie, $id, null);
                            $this->pneuMouvementService->sortieMouvement(
                                $this->buildMouvementData($serie, $id, 'AFFECTATION', 'AFF-'.Carbon::now()->format('Y-m-d-H:i:s'), 'AFFECTATION PNEU SUR VEHICULE', $detail['etat_piece'] ?? null)
                            );
                        }
                    }
                }
            }

            // 3 - Traitement des documents administratifs
            foreach ($documents as $doc) {
                if (isset($doc['fichier']) && $doc['fichier'] instanceof \Illuminate\Http\UploadedFile) {
                    $this->documentDynamicService->saveDocument(
                        $element,
                        $doc['document_type_id'],
                        $doc['fichier'],
                        $doc['date_expiration'] ?? null,
                        $doc['observation'] ?? null
                    );
                }
            }


            DB::commit();

            return $this->successResponse('Insertion terminée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }
    }

    /**
     * Met a jour un élément existant et sais détails
     *
     * @param mixed $model instance de l'éléments
     * @param array $validated
     * @return array ['error' => bool, 'message' => string]
     */
    public function update($model, array $validated): array
    {
        DB::beginTransaction();

        try {
            // Extraction des documents
            $documents = $validated['documents'] ?? [];
            unset($validated['documents']);

            $id = $model->id;
            // 1. Suppression des anciens détails
            $this->vehiculeElementService->deleteByElementID($id);

            // 2. Mise à jour de l'élément principal
            $data_vehicule = $this->extractVehicule($validated);
            $this->repository->update($model, $data_vehicule);

            // 3. Gestion des pneus (affectations/désaffectations)
            $newAssignedNumeroSeries = $this->computeNewAssignedPneuNumeroSeries($validated['element_vehicules'] ?? []);

            //fonction pour recupérer les pneus déjà affectés au véhicule
            $prevAssignedNumeroSeries = $this->pneuSerieService->getAssignedNumeroSeriesByVehicule($id);

            //fonction pour recupérer les pneus à enlever
            $toDetach = array_values(array_diff($prevAssignedNumeroSeries, $newAssignedNumeroSeries));
            //fonction pour recupérer les pneus à ajouter
            $toAttach = array_values(array_diff($newAssignedNumeroSeries, $prevAssignedNumeroSeries));
            //fonction pour recupérer les pneus à garder
            $toKeep   = array_values(array_intersect($prevAssignedNumeroSeries, $newAssignedNumeroSeries));

            // Détacher les pneus supprimés et créer un mouvement
            foreach ($toDetach as $numeroSerie) {
                $serie = $this->pneuSerieService->getByNumeroSerie($numeroSerie);
                if ($serie) {
                    $serie->position_actuel  = null;
                    $serie->position_initial = null;
                    $serie->is_first         = false;
                    $serie->save();
                    $this->pneuSerieService->detachFromVehiculeByNumeroSerie($numeroSerie);
                    $this->pneuMouvementService->entrerMouvement(
                        $this->buildMouvementData($serie, $id, 'DETACHEMENT', 'DETACHEMENT-'.Carbon::now()->format('Y-m-d-H:i:s'), 'DETACHEMENT PNEU DU VEHICULE')
                    );
                }
            }

            // Mettre à jour position_actuel (et position_initial si is_first) des pneus conservés
            foreach ($toKeep as $numeroSerie) {
                $serie = $this->pneuSerieService->getByNumeroSerie($numeroSerie);
                if ($serie) {
                    $elementDetail = collect($validated['element_vehicules'] ?? [])
                        ->first(fn($el) => ($el['numero_serie'] ?? null) === $numeroSerie);
                    $emplacement = $elementDetail['emplacement'] ?? null;
                    $isFirst = boolval($elementDetail['is_first'] ?? false);
                    if (!empty($emplacement)) {
                        if ($isFirst) {
                            $serie->position_initial = $emplacement;
                        }
                        $serie->position_actuel = $emplacement;
                    }
                    $serie->is_first = $isFirst;
                    $serie->save();
                }
            }

            // Attacher les nouveaux pneus et créer un mouvement
            foreach ($toAttach as $numeroSerie) {
                $serie = $this->pneuSerieService->getByNumeroSerie($numeroSerie);
                if ($serie) {
                    $elementDetail = collect($validated['element_vehicules'] ?? [])
                        ->first(fn($el) => ($el['numero_serie'] ?? null) === $numeroSerie);
                    if (!empty($elementDetail['date_montage'])) {
                        $serie->date_montage = $elementDetail['date_montage'];
                    }
                    if (!empty($elementDetail['etat_piece'])) {
                        $serie->etat = $elementDetail['etat_piece'];
                    }
                    $emplacement = $elementDetail['emplacement'] ?? null;
                    if (!empty($emplacement)) {
                        if ($elementDetail['is_first'] ?? false) {
                            $serie->position_initial = $emplacement;
                        }
                        $serie->position_actuel = $emplacement;
                    }
                    $serie->is_first = boolval($elementDetail['is_first'] ?? false);
                    $serie->save();
                    $this->pneuSerieService->assignToVehiculeByNumeroSerie($numeroSerie, $id, null);
                    $this->pneuMouvementService->sortieMouvement(
                        $this->buildMouvementData($serie, $id, 'AFFECTATION', 'AFF-'.Carbon::now()->format('Y-m-d-H:i:s'), 'AFFECTATION PNEU SUR VEHICULE', $elementDetail['etat_piece'] ?? null)
                    );
                }
            }

            // 4. Création des nouveaux détails
            if (!empty($validated['element_vehicules'])) {
                $this->createDetails($id, $validated['element_vehicules']);
            }

            // 5. Traitement des documents administratifs (nouveaux uploads)
            foreach ($documents as $doc) {
                if (isset($doc['fichier']) && $doc['fichier'] instanceof \Illuminate\Http\UploadedFile) {
                    $this->documentDynamicService->saveDocument(
                        $model,
                        $doc['document_type_id'],
                        $doc['fichier'],
                        $doc['date_expiration'] ?? null,
                        $doc['observation'] ?? null
                    );
                }
            }

            DB::commit();

            return $this->successResponse('Mise à jour effectuée avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la mise à jour', $e);
        }
    }


    /**
     * Supprimer un élément et ses détails associés
     *
     * @param mixed $model Instance de l'élément
     * @return array
     */
    public function delete($model): array
    {
        DB::beginTransaction();

        try {
            // 1. Supprimer d'abord tous les détails associés
            $this->vehiculeElementService->deleteByElementId($model->id);

            // 2. Supprimer l'élément principal
            $this->repository->delete($model);

            DB::commit();

            return $this->successResponse('Élément supprimé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la suppression', $e);
        }
    }

    /**
     * crée les détails d'un éléments
     *
     * @param int $elementId ID de l'élément parent
     * @param array $details liste des détails à créer
     * @return void
     */
    private function createDetails(int $elementId, array $details): void
    {
        $excluded = ['is_first'];
        foreach ($details as $detail) {
            $this->vehiculeElementService->create([
                ...array_diff_key($detail, array_flip($excluded)),
                'vehicule_id' => $elementId
            ]);
        }
    }

    private function buildMouvementData(PneuSerie $serie, int $vehiculeId, string $typeMvt, string $reference, string $commentaire, ?string $etat = null): array
    {
        $serie->loadMissing('approDetail');

        return [
            'pneu_serie_id'             => $serie->id,
            'article_id'                => $serie->article_id,
            'numero_serie'              => $serie->numero_serie,
            'etat'                      => $etat ?? $serie->etat,
            'magasin_id'                => $serie->approDetail?->magasin_id,
            'vehicule_id'               => $vehiculeId,
            'remorque_id'               => null,
            'type_mvt'                  => $typeMvt,
            'date_mvt'                  => Carbon::now(),
            'date_heure_enregistrement' => Carbon::now()->format('Y-m-d H:i:s'),
            'reference_mvt'             => $reference,
            'commentaire'               => $commentaire,
            'user_id'                   => Auth::id(),
        ];
    }

    //Extrait les numero_serie de pneus à affecter à partir des détails saisis
    private function computeNewAssignedPneuNumeroSeries(array $elementVehicules): array
    {
        return collect($elementVehicules)
            ->filter(function ($detail) {
                return ($detail['is_pneu'] ?? false) && !empty($detail['numero_serie']);
            })
            ->map(function ($detail) {
                return $detail['numero_serie']; // Maintenant c'est directement le numero_serie
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     *
     * @param int $vehiculeId
     * @return array
     */
    public function getVehiculeDetails(int $vehiculeId): array
    {
        $vehicule = $this->repository->find($vehiculeId);
        if (!$vehicule) {
            return ['error' => true, 'message' => 'Véhicule non trouvé'];
        }

        $photos = $this->vehiculePhotoService->getPhotosWithDecodedImages($vehiculeId);
        $documents = $this->vehiculedocumentService->getDocumentWithDecoded($vehiculeId);
        //        $elements = $this->vehiculeElementService->getElementsByVehicule($vehiculeId);
        return [
            'data' => [
                'vehicule' => $vehicule->toArray(),
                'photos' => collect($photos)->toArray(),
                'documents' => collect($documents)->toArray(),

            ]
        ];
    }


    public function fetchDistinctColumn(string $column = 'couleur')
    {
        return $this->repository->fetchDistinctByColumn($column);
    }

    /**
     * Récupère les statistiques des véhicules avec filtres optimisés
     */
    public function getVehiculeStat(array $filtre)
    {
        return $this->repository->getVehiculeStats($filtre, $this->scope);
    }

    /**
     * Récupère le total des véhicules avec filtres
     */
    public function getTotalVehicule($filtre)
    {
        return parent::countElement($filtre);
    }

    /**
     * Retourne la liste des véhicules avec chauffeurs et remorque.
     */
    public function getVehiculesWithChauffeursAndRemorque(): array
    {
        return $this->repository->fetchData(['chauffeurs', 'remorque'])
            ->map(function ($vehicule) {
                return [
                    'id' => $vehicule->id,
                    'marque' => $vehicule->marque,
                    'modele' => $vehicule->modele,
                    'immatriculation' => $vehicule->immatriculation,
                    'chauffeurs' => $vehicule->chauffeurs->map(function ($c) {
                        return [
                            'id' => $c->id,
                            'nom' => $c->nom,
                            'prenom' => $c->prenom,
                        ];
                    })->toArray(),
                    'remorque' => $vehicule->remorque ? [
                        'id' => $vehicule->remorque->id,
                        'numero_remorque' => $vehicule->remorque->numero_remorque,
                    ] : null,
                ];
            })->toArray();
    }
}
