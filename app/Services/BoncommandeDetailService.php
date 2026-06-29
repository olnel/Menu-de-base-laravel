<?php

namespace App\Services;

use App\Repositories\BoncommandeDetailRepository;
use App\Services\Base\BaseService;

class BoncommandeDetailService extends BaseService
{
    protected $repository;
    public function __construct( BoncommandeDetailRepository $boncommandeDetailRepository)
    {
        $this->repository = $boncommandeDetailRepository;
        parent::__construct($boncommandeDetailRepository);
    }

    public function deleteByBonCommande(mixed $id)
    {
        $this->repository->deleteByBonCommande($id);
    }
}
