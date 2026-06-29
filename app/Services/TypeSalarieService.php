<?php

namespace App\Services;

use App\Repositories\TypeSalarieRepository;
use App\Services\Base\BaseService;

class TypeSalarieService extends BaseService
{
    protected $repository;

    public function __construct(TypeSalarieRepository $typeSalarieRepository)
    {
        $this->repository = $typeSalarieRepository;
        parent::__construct($typeSalarieRepository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('libelle')->setFilterValue('id');
    }
}
