<?php

namespace App\Repositories;

use App\Models\Immobilisation;

class ImmobilisationRepository extends BaseRepository
{
    public function __construct(Immobilisation $immobilisation)
    {
        parent::__construct($immobilisation);
    }

}
