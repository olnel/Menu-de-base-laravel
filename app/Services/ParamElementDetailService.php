<?php

namespace App\Services;

use App\Repositories\ParamElementDetailRepository;
use App\Services\Base\BaseService;

class ParamElementDetailService extends BaseService
{

    protected $repository;
    public function __construct(ParamElementDetailRepository $paramElementDetailRepository)
    {
        $this->repository = $paramElementDetailRepository;
        parent::__construct($paramElementDetailRepository);
    }
    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('emplacement');
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
}
