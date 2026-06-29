<?php

namespace App\Repositories;

use App\Models\FactureClientReglement;

class FactureclientReglementRepository extends BaseRepository
{
    public function __construct(FactureClientReglement $model)
    {
        parent::__construct($model);
    }

}
