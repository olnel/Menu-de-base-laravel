<?php

namespace App\Repositories;

use App\Models\Tarif;

class TarifRepository extends BaseRepository
{
    public function __construct(Tarif $tarif)
    {
        parent::__construct($tarif);
    }
}
