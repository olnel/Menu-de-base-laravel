<?php

namespace App\Repositories;

use App\Models\PneuMouvement;

class PneuMouvementRepository extends BaseRepository
{
    public function __construct(PneuMouvement $pneuMouvement)
    {
        parent::__construct($pneuMouvement);
    }
}
