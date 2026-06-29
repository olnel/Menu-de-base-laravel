<?php

namespace App\Repositories;

use App\Models\Paie;

class PaieRepository extends BaseRepository
{
    public function __construct(Paie $model)
    {
        parent::__construct($model);
    }
}
