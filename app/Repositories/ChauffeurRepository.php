<?php

namespace App\Repositories;

use App\Models\Chauffeur;

class ChauffeurRepository extends BaseRepository
{
    public function __construct(Chauffeur $chauffeur)
    {
        parent::__construct($chauffeur);
    }

    public function getModel()
    {
        return $this->model;
    }
}
