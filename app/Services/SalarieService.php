<?php

namespace App\Services;

use App\Models\Chauffeur;
use App\Models\TypeSalarie;
use App\Repositories\SalarieRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class SalarieService extends BaseService
{
    protected $repository;
    protected array $relation = ['typeSalarie', 'documents', 'documents.documentType', 'chauffeur'];
    protected array $scope = [
        'filter' => 'search',
        'onlyTrashed' => 'only_trashed'
    ];

    public function __construct(
        SalarieRepository $salarieRepository
    ) {
        $this->repository = $salarieRepository;
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
            $vehicule_id = $validated['vehicule_id'] ?? null;
            $parent_chauffeur_id = $validated['parent_chauffeur_id'] ?? null;
            unset($validated['vehicule_id'], $validated['parent_chauffeur_id'], $validated['documents']);

            $numData = $this->generateMatricule();
            $validated['matricule'] = $numData['matricule'];
            $validated['count_matricule'] = $numData['count_matricule'];

            if (isset($validated['photo']) && $validated['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $filename = 'salarie_' . time() . '.' . $validated['photo']->getClientOriginalExtension();
                $validated['photo'] = $validated['photo']->storeAs('salaries/photos', $filename, 'public');
            }

            $element = $this->repository->create($validated);

            $this->syncChauffeur($element, [
                'vehicule_id' => $vehicule_id,
                'parent_chauffeur_id' => $parent_chauffeur_id
            ]);

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
            $vehicule_id = $validated['vehicule_id'] ?? null;
            $parent_chauffeur_id = $validated['parent_chauffeur_id'] ?? null;
            unset($validated['vehicule_id'], $validated['parent_chauffeur_id'], $validated['documents']);
            unset($validated['matricule'], $validated['count_matricule']);

            if (isset($validated['photo']) && $validated['photo'] instanceof \Illuminate\Http\UploadedFile) {
                if ($model->photo) {
                    Storage::disk('public')->delete($model->photo);
                    Storage::disk('public')->delete(str_replace('.webp', '_thumb.webp', $model->photo));
                }

                $filename = 'salarie_' . time() . '.' . $validated['photo']->getClientOriginalExtension();
                $validated['photo'] = $validated['photo']->storeAs('salaries/photos', $filename, 'public');
            } else {
                unset($validated['photo']);
            }

            $this->repository->update($model, $validated);

            $this->syncChauffeur($model, [
                'vehicule_id' => $vehicule_id,
                'parent_chauffeur_id' => $parent_chauffeur_id
            ]);

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
                Storage::disk('public')->delete($model->photo);
                Storage::disk('public')->delete(str_replace('.webp', '_thumb.webp', $model->photo));
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
                $chauffeur->update($data);
            } else {
                Chauffeur::create($data);
            }
        } else {
            if ($salarie->chauffeur) {
                $salarie->chauffeur->delete();
            }
        }
    }
}
