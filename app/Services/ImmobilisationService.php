<?php

namespace App\Services;

use App\Repositories\ImmobilisationRepository;
use App\Services\Base\BaseService;

class ImmobilisationService extends BaseService
{
    protected $repository;
    public function __construct(ImmobilisationRepository $immobilisationRepository)
    {
        $this->repository = $immobilisationRepository;
        parent::__construct($immobilisationRepository);
    }
}
