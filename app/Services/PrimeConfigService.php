<?php

namespace App\Services;

use App\Repositories\PrimeConfigRepository;
use App\Services\Base\BaseService;

class PrimeConfigService extends BaseService
{
    protected $repository;

    public function __construct(PrimeConfigRepository $primeConfigRepository)
    {
        $this->repository = $primeConfigRepository;
        parent::__construct($primeConfigRepository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('libelle')->setFilterValue('id');
    }
}
