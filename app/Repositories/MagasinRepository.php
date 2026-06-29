<?php

namespace App\Repositories;

use App\Models\Magasin;

class MagasinRepository extends BaseRepository
{
    public function __construct(Magasin $magasin)
    {
        parent::__construct($magasin);
    }
}
