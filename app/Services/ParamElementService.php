<?php

namespace App\Services;

use App\Repositories\ParamElementRepository;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;

class ParamElementService extends BaseService
{
    protected $repository;
    protected $detail_service;

    protected array $relation = ['details'];

    /**
     * Initialisation avec injection de dépendances
     *
     * @param ParamElementRepository $paramElementRepository
     * @param ParamElementDetailService $paramElementDetailService
     */
    public function __construct(ParamElementRepository $paramElementRepository,
                                ParamElementDetailService $paramElementDetailService)
    {
        $this->detail_service = $paramElementDetailService;
        $this->repository = $paramElementRepository;
        parent::__construct($paramElementRepository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
              ->setFilterLabel('immatriculation');
    }

    /**
     * Crée un nouvel élément avec ses détails
     *
     * @param array $validated donnée validées
     * @return array ['error' => bool, 'message' => string]
     */
    public function create(array $validated): array
    {
        DB::beginTransaction();

        try {

            //1- Création de l'élément principale
            $element= $this->repository->create(['type_model' => $validated['type_model']]);
            $id = $element->id;

            //2-Création des détails associés
            $this->createDetails($id, $validated['details']);

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
    public function update($model, array $validated):array
    {
        DB::beginTransaction();

        try {
            $id = $validated['id'];
            // 1. Suppression des anciens détails
            $this->detail_service->deleteByElementID($id);

            // 2. Mise à jour de l'élément principal
            $this->repository->update($model, ['type_model' => $validated['type_model']]);

            // 3. Création des nouveaux détails
            $this->createDetails($validated['id'], $validated['details']);
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
            $this->detail_service->deleteByElementId($model->id);

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
        foreach ($details as $detail) {
            $this->detail_service->create([
                ...$detail,
                'param_element_id' => $elementId
            ]);
        }
    }
}
