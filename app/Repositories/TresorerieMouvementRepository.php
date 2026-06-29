<?php

namespace App\Repositories;

use App\Models\TresorerieMouvement;

class TresorerieMouvementRepository extends BaseRepository
{
    public function __construct(TresorerieMouvement $tresorerieMouvement)
    {
        parent::__construct($tresorerieMouvement);
    }
}
