<?php

namespace App\Services;

use App\Models\PneuSerie;
use App\Repositories\RemorqueRepository;
use App\Services\Base\BaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemorqueService extends BaseService
{
    protected $remorqueElementService;
    protected $repository;
    protected $remorquePhotoService, $remorqueDocumentService;
    protected DocumentDynamicService $documentDynamicService;
    protected array $relation = ['element_remorques.pneuSerie', 'documents', 'documents.documentType'];

    protected array $scope = [
        'filter' => 'search',
        'filterdatestart' => 'start_date',
        'filterdateend' => 'end_date',
        'filterchauffeur' => 'chauffeur_id',
        'filterclient' => 'client_id',
    ];

    public function __construct(
        RemorqueRepository $remorqueRepository,
        RemorqueElementService $remorqueElementService,
        RemorquePhotoService $remorquePhotoService,
        RemorqueDocumentService $remorqueDocumentService,
        protected readonly PneuSerieService $pneuSerieService,
        protected readonly PneuMouvementService $pneuMouvementService,
        DocumentDynamicService $documentDynamicService
    ) {
        $this->repository = $remorqueRepository;
        $this->remorqueElementService = $remorqueElementService;
        $this->remorquePhotoService = $remorquePhotoService;
        $this->remorqueDocumentService = $remorqueDocumentService;
        $this->documentDynamicService = $documentDynamicService;

        parent::__construct($remorqueRepository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('numero_remorque');
    }

    private function extractData(array $validated): array
    {
        return [
            'param_element_id' => $validated['param_element_id'],
            'numero_remorque'  => $validated['numero_remorque'],
            'marque_remorque'  => $validated['marque_remorque'],
            'modele_remorque'  => $validated['modele_remorque'],
        ];
    }

    public function create(array $validated): array
    {
        DB::beginTransaction();
        try {
            // Extraction des documents
            $documents = $validated['documents'] ?? [];
            unset($validated['documents']);

            $element = $this->repository->create($this->extractData($validated));
            $id = $element->id;

            if (!empty($validated['element_remorques'])) {
                $this->createDetails($id, $validated['element_remorques']);

                foreach ($validated['element_remorques'] as $detail) {
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
                            $this->pneuSerieService->assignToVehiculeByNumeroSerie($numeroSerie, null, $id);
                            $this->pneuMouvementService->sortieMouvement(
                                $this->buildMouvementData($serie, $id, 'AFFECTATION', 'AFF-'.Carbon::now()->format('Y-m-d-H:i:s'), 'AFFECTATION PNEU SUR REMORQUE', $detail['etat_piece'] ?? null)
                            );
                        }
                    }
                }
            }

            // Traitement des documents administratifs
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

    public function update($model, array $validated): array
    {
        DB::beginTransaction();
        try {
            // Extraction des documents
            $documents = $validated['documents'] ?? [];
            unset($validated['documents']);

            $id = $model->id;

            $this->remorqueElementService->deleteByElementID($id);

            $this->repository->update($model, $this->extractData($validated));

            $newAssignedNumeroSeries = $this->computeNewAssignedPneuNumeroSeries($validated['element_remorques'] ?? []);
            $prevAssignedNumeroSeries = $this->pneuSerieService->getAssignedNumeroSeriesByRemorque($id);

            $toDetach = array_values(array_diff($prevAssignedNumeroSeries, $newAssignedNumeroSeries));
            $toAttach = array_values(array_diff($newAssignedNumeroSeries, $prevAssignedNumeroSeries));
            $toKeep   = array_values(array_intersect($prevAssignedNumeroSeries, $newAssignedNumeroSeries));

            foreach ($toDetach as $numeroSerie) {
                $serie = $this->pneuSerieService->getByNumeroSerie($numeroSerie);
                if ($serie) {
                    $serie->position_actuel  = null;
                    $serie->position_initial = null;
                    $serie->is_first         = false;
                    $serie->save();
                    $this->pneuSerieService->detachFromVehiculeByNumeroSerie($numeroSerie);
                    $this->pneuMouvementService->entrerMouvement(
                        $this->buildMouvementData($serie, $id, 'DETACHEMENT', 'DETACHEMENT-'.Carbon::now()->format('Y-m-d-H:i:s'), 'DETACHEMENT PNEU DE LA REMORQUE')
                    );
                }
            }

            foreach ($toAttach as $numeroSerie) {
                $serie = $this->pneuSerieService->getByNumeroSerie($numeroSerie);
                if ($serie) {
                    $elementDetail = collect($validated['element_remorques'] ?? [])
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
                    $this->pneuSerieService->assignToVehiculeByNumeroSerie($numeroSerie, null, $id);
                    $this->pneuMouvementService->sortieMouvement(
                        $this->buildMouvementData($serie, $id, 'AFFECTATION', 'AFF-'.Carbon::now()->format('Y-m-d-H:i:s'), 'AFFECTATION PNEU SUR REMORQUE', $elementDetail['etat_piece'] ?? null)
                    );
                }
            }

            // Mettre à jour position_actuel (et position_initial si is_first) des pneus conservés
            foreach ($toKeep as $numeroSerie) {
                $serie = $this->pneuSerieService->getByNumeroSerie($numeroSerie);
                if ($serie) {
                    $elementDetail = collect($validated['element_remorques'] ?? [])
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

            if (!empty($validated['element_remorques'])) {
                $this->createDetails($id, $validated['element_remorques']);
            }

            // Traitement des documents administratifs (nouveaux uploads)
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

    public function delete($model): array
    {
        DB::beginTransaction();
        try {
            $this->remorqueElementService->deleteByElementId($model->id);
            $this->repository->delete($model);
            DB::commit();
            return $this->successResponse('Élément supprimé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la suppression', $e);
        }
    }

    private function createDetails(int $elementId, array $details): void
    {
        $excluded = ['is_first'];
        foreach ($details as $detail) {
            $this->remorqueElementService->create([
                ...array_diff_key($detail, array_flip($excluded)),
                'remorque_id' => $elementId
            ]);
        }
    }

    private function buildMouvementData(PneuSerie $serie, int $remorqueId, string $typeMvt, string $reference, string $commentaire, ?string $etat = null): array
    {
        $serie->loadMissing('approDetail');

        return [
            'pneu_serie_id'             => $serie->id,
            'article_id'                => $serie->article_id,
            'numero_serie'              => $serie->numero_serie,
            'etat'                      => $etat ?? $serie->etat,
            'magasin_id'                => $serie->approDetail?->magasin_id,
            'vehicule_id'               => null,
            'remorque_id'               => $remorqueId,
            'type_mvt'                  => $typeMvt,
            'date_mvt'                  => Carbon::now(),
            'date_heure_enregistrement' => Carbon::now()->format('Y-m-d H:i:s'),
            'reference_mvt'             => $reference,
            'commentaire'               => $commentaire,
            'user_id'                   => Auth::id(),
        ];
    }

    private function computeNewAssignedPneuNumeroSeries(array $elementRemorques): array
    {
        return collect($elementRemorques)
            ->filter(fn($detail) => ($detail['is_pneu'] ?? false) && !empty($detail['numero_serie']))
            ->map(fn($detail) => $detail['numero_serie'])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function getVehiculeDetails(int $vehiculeId): array
    {
        $vehicule = $this->repository->find($vehiculeId);
        if (!$vehicule) {
            return ['error' => true, 'message' => 'Remorque non trouvée'];
        }

        $photos = $this->remorquePhotoService->getPhotosWithDecodedImages($vehiculeId);
        $documents = $this->remorqueDocumentService->getDocumentWithDecoded($vehiculeId);

        return [
            'data' => [
                'vehicule'  => $vehicule->toArray(),
                'photos'    => collect($photos)->toArray(),
                'documents' => collect($documents)->toArray(),
            ]
        ];
    }

    public function fetchDistinctColumn(string $column = 'modele_remorque')
    {
        return $this->repository->fetchDistinctByColumn($column);
    }

    public function getTotalRemorque($filtre)
    {
        return parent::countElement($filtre);
    }

    public function getRemorqueData($filtre)
    {
        return $this->repository->getRemorqueStats($filtre, $this->scope);
    }
}
