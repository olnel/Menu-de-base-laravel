<?php

namespace App\Repositories;

use App\Models\LibelleMaintenance;

class LibelleMaintenanceRepository extends BaseRepository
{
    public function __construct(LibelleMaintenance $libelleMaintenance)
    {
        parent::__construct($libelleMaintenance);
    }

    public function findElement(array $critere)
    {
        return $this->model->where($critere)->first();
    }
}
