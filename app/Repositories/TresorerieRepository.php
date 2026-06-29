<?php

namespace App\Repositories;

use App\Models\Tresorerie;

class TresorerieRepository extends BaseRepository
{
    public function __construct(Tresorerie $tresorerie)
    {
        parent::__construct($tresorerie);
    }
}
