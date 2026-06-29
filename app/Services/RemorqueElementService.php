<?php

namespace App\Services;

use App\Repositories\RemorqueElementRepository;
use App\Services\Base\BaseService;

class RemorqueElementService extends BaseService
{
    protected $repository;
    public function __construct(RemorqueElementRepository $remorqueElementRepository)
    {
        $this->repository = $remorqueElementRepository;
        parent::__construct($remorqueElementRepository);
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
