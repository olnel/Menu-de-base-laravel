<?php

namespace App\Services;

use App\Repositories\DevisClientDetailsRepository;
use App\Services\Base\BaseService;

class DevisClientDetailsService extends BaseService
{
    protected $repository;

    public function __construct(DevisClientDetailsRepository $clientDetailsRepository)
    {
        $this->repository = $clientDetailsRepository;
        parent::__construct($clientDetailsRepository);
    }
}
