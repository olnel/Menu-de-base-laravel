<?php

namespace App\Services;

use App\Repositories\FournisseurRepository;
use App\Services\Base\BaseService;

class FournisseurService extends BaseService
{
    protected $repository;
    public function __construct(FournisseurRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('nom_fournisseur')
            ->setFilterLabel('nom_fournisseur');
    }

    public function updateOrCreateStock(string $nom_fournisseur): array
    {
        $element = $this->repository->findElement(['nom_fournisseur' => $nom_fournisseur]);
        if ($element) {
           return parent::update($element, ['nom_fournisseur' => $nom_fournisseur]);
        }else{
           return parent::create(['nom_fournisseur' => $nom_fournisseur]);
        }
    }
}
