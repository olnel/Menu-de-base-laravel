<?php

namespace App\Services;

use App\Repositories\LibelleMaintenanceRepository;
use App\Services\Base\BaseService;

class LibelleMaintenanceService extends BaseService
{
    protected $repository;

    public function __construct(LibelleMaintenanceRepository $libelleMaintenanceRepository)
    {
        $this->repository = $libelleMaintenanceRepository;
        parent::__construct($libelleMaintenanceRepository);
    }

    /**
     * fonction permet de verifier l'existance d'un element
     *
     * @param array $data_element
     * @return void
     */
    public function isExiste(array $data_element):int
    {

        $element =$this->repository->findElement(['libelle' => $data_element['libelle']]);
        if ($element){ // si trouvé alors on fait modification
            parent::update($element, $data_element);
        }else{ // on insert
            $data = parent::create($data_element);
            $element= $data['element'];
        }
        return $element->id;
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('libelle');
    }
}
