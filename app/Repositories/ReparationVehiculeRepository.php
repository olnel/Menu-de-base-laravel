<?php

namespace App\Repositories;

use App\Models\ReparationVehicule;

class ReparationVehiculeRepository extends BaseRepository
{
    public function __construct(ReparationVehicule $reparationVehicule)
    {
        parent::__construct($reparationVehicule);
    }
}
