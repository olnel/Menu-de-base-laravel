<?php

namespace App\Services;

use App\Repositories\CarburantCardRepository;
use App\Repositories\VoyageAffectationRepository;
use App\Services\Base\BaseService;

class VoyageAffectationService extends BaseService
{
    protected $repository;

    public function __construct(VoyageAffectationRepository $magasinRepository)
    {
        $this->repository = $magasinRepository;
        parent::__construct($magasinRepository);
    }

    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('nom_magasin');
    }
}
