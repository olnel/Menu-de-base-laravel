<?php

namespace App\Repositories;

use App\Models\Fournisseur;

class FournisseurRepository extends BaseRepository
{
    public function __construct(Fournisseur $fournisseur)
    {
        parent::__construct($fournisseur);
    }

}
