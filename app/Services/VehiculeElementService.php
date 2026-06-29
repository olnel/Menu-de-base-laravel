<?php

namespace App\Services;

use App\Repositories\VehiculeElementRepository;
use App\Services\Base\BaseService;

class VehiculeElementService extends BaseService
{
    protected $repository;
    public function __construct(VehiculeElementRepository $vehiculeElementRepository)
    {
        $this->repository = $vehiculeElementRepository;
        parent::__construct($vehiculeElementRepository);
    }

    /**
     * Supprimer tous les détails associés à un élément
     *
     * @param int $elementIS ID de l'élément parent
     * @return int
     */
    public function deleteByElementID(int $elementIS)
    {
        return $this->repository->deleteByElementId($elementIS);
    }

    public function getElementsByVehicule(int $vehiculeId)
    {
        return $this->repository->getElementsByVehicule($vehiculeId);
    }

    public function fetchDistinctEmplacement()
    {
        return $this->repository->fetchDistinctByColumn();
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('libelle');
    }

}
