<?php

namespace App\Services;

use App\Repositories\SalarieRepository;
use App\Repositories\ChauffeurRepository;
use App\Models\TypeSalarie;
use App\Services\Base\BaseService;
use App\Services\DocumentDynamicService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class SalarieService extends BaseService
{
    protected $repository;
    protected $chauffeurRepository;
    protected array $relation = ['typeSalarie', 'documents', 'documents.documentType', 'chauffeur'];
    protected array $scope = [
        'filter' => 'search',
        'onlyTrashed' => 'only_trashed'
    ];
    protected ImageService $imageService;
    protected DocumentDynamicService $documentService;

    public function __construct(
        SalarieRepository $salarieRepository, 
        ChauffeurRepository $chauffeurRepository,
        ImageService $imageService,
        DocumentDynamicService $documentService
    ) {
        $this->repository = $salarieRepository;
        $this->chauffeurRepository = $chauffeurRepository;
        $this->imageService = $imageService;
        $this->documentService = $documentService;
        parent::__construct($salarieRepository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('nom')->setFilterValue('id');
    }

    private function getNextMatriculeCount(): int
    {
        $lastSalarie = $this->repository->findLastSalarie();
        return $lastSalarie ? $lastSalarie->count_matricule + 1 : 1;
    }

    public function generateMatricule(): array
    {
        $count = $this->getNextMatriculeCount();
        $matricule = sprintf('SAL-%03d', $count);
        return [
            'count_matricule' => $count,
            'matricule' => $matricule
        ];
    }

    public function create(array $validated): array
    {
        DB::beginTransaction();
        try {
            // Extraction des documents et infos chauffeur si présents
            $documents = $validated['documents'] ?? [];
            unset($validated['documents']);

            $vehicule_id = $validated['vehicule_id'] ?? null;
            $parent_chauffeur_id = $validated['parent_chauffeur_id'] ?? null;
            unset($validated['vehicule_id'], $validated['parent_chauffeur_id']);

            // Génération automatique du matricule
            $numData = $this->generateMatricule();
            $validated['matricule'] = $numData['matricule'];
            $validated['count_matricule'] = $numData['count_matricule'];

            // Gestion de la photo
            if (isset($validated['photo']) && $validated['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $photo = $validated['photo'];
                $path = 'salaries/photos/' . time();
                $processed = $this->imageService->processWithMemorySafety($photo, $path, ['create_thumb' => true]);
                $validated['photo'] = $processed['main']['path'];
            }

            $element = $this->repository->create($validated);

            // Synchronisation avec la table chauffeurs
            $this->syncChauffeur($element, [
                'vehicule_id' => $vehicule_id,
                'parent_chauffeur_id' => $parent_chauffeur_id
            ]);

            // Traitement des documents
            foreach ($documents as $doc) {
                if (isset($doc['fichier']) && $doc['fichier'] instanceof \Illuminate\Http\UploadedFile) {
                    $this->documentService->saveDocument(
                        $element,
                        $doc['document_type_id'],
                        $doc['fichier'],
                        $doc['date_expiration'] ?? null,
                        $doc['observation'] ?? null
                    );
                }
            }

            DB::commit();
            return $this->successResponse('Salarié enregistré avec succès', $element);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur creation salarié: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'enregistrement', $e);
        }
    }

    public function update($model, array $validated): array
    {
        DB::beginTransaction();
        try {
            // Extraction des documents et infos chauffeur si présents
            $documents = $validated['documents'] ?? [];
            unset($validated['documents']);

            $vehicule_id = $validated['vehicule_id'] ?? null;
            $parent_chauffeur_id = $validated['parent_chauffeur_id'] ?? null;
            unset($validated['vehicule_id'], $validated['parent_chauffeur_id']);

            // Empêcher la modification du matricule via l'update si passé par erreur
            unset($validated['matricule']);
            unset($validated['count_matricule']);

            if (isset($validated['photo']) && $validated['photo'] instanceof \Illuminate\Http\UploadedFile) {
                // Supprimer l'ancienne photo si elle existe
                if ($model->photo) {
                    $this->imageService->delete($model->photo);
                    // Supprimer aussi la miniature si elle existe (convention _thumb.webp)
                    $thumbPath = str_replace('.webp', '_thumb.webp', $model->photo);
                    $this->imageService->delete($thumbPath);
                }

                $photo = $validated['photo'];
                $path = 'salaries/photos/' . time();
                $processed = $this->imageService->processWithMemorySafety($photo, $path, ['create_thumb' => true]);
                $validated['photo'] = $processed['main']['path'];
            } else {
                // Si pas de nouvelle photo, on garde l'ancienne
                unset($validated['photo']);
            }

            $this->repository->update($model, $validated);

            // Synchronisation avec la table chauffeurs
            $this->syncChauffeur($model, [
                'vehicule_id' => $vehicule_id,
                'parent_chauffeur_id' => $parent_chauffeur_id
            ]);

            // Traitement des documents (uniquement les nouveaux uploads)
            foreach ($documents as $doc) {
                if (isset($doc['fichier']) && $doc['fichier'] instanceof \Illuminate\Http\UploadedFile) {
                    $this->documentService->saveDocument(
                        $model,
                        $doc['document_type_id'],
                        $doc['fichier'],
                        $doc['date_expiration'] ?? null,
                        $doc['observation'] ?? null
                    );
                }
            }

            DB::commit();
            return $this->successResponse('Salarié mis à jour avec succès', $model);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur update salarié: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour', $e);
        }
    }

    public function delete($model)
    {
        DB::beginTransaction();
        try {
            if ($model->photo) {
                $this->imageService->delete($model->photo);
                $thumbPath = str_replace('.webp', '_thumb.webp', $model->photo);
                $this->imageService->delete($thumbPath);
            }
            $this->repository->delete($model);
            DB::commit();
            return $this->successResponse('Salarié supprimé avec succès');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de la suppression', $e);
        }
    }

    /**
     * Synchronise les informations avec la table chauffeurs
     */
    private function syncChauffeur($salarie, array $chauffeurData): void
    {
        $type = $salarie->typeSalarie;
        if (!$type) {
            $type = TypeSalarie::find($salarie->type_salarie_id);
        }

        if (!$type) return;

        $libelleType = strtolower($type->libelle);
        $isChauffeur = str_contains($libelleType, 'chauffeur');
        $isAide = str_contains($libelleType, 'aide');

        if ($isChauffeur) {
            $data = [
                'matricule' => $salarie->matricule,
                'nom' => $salarie->nom,
                'prenom' => $salarie->prenom,
                'date_naissance' => $salarie->date_naissance,
                'CIN' => $salarie->cin,
                'telephone' => $salarie->telephone,
                'adresse' => $salarie->adresse,
                'img' => $salarie->photo,
                'salarie_id' => $salarie->id,
                'is_aide_chauffeur' => $isAide,
                'parent_chauffeur_id' => $chauffeurData['parent_chauffeur_id'] ?? null,
                'vehicule_id' => $chauffeurData['vehicule_id'] ?? null,
            ];

            $chauffeur = $salarie->chauffeur;
            if ($chauffeur) {
                $this->chauffeurRepository->update($chauffeur, $data);
            } else {
                $this->chauffeurRepository->create($data);
            }
        } else {
            // Si le salarié n'est plus un chauffeur, on supprime l'entrée correspondante
            if ($salarie->chauffeur) {
                $this->chauffeurRepository->delete($salarie->chauffeur);
            }
        }
    }
}
