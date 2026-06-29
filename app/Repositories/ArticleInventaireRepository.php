<?php

namespace App\Repositories;

use App\Models\ArticleInventaire;

class ArticleInventaireRepository extends BaseRepository
{
    public function __construct(ArticleInventaire $articleInventaire)
    {
        parent::__construct($articleInventaire);

    }
    // Your Repository methods go here
}
