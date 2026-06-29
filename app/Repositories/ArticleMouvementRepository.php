<?php

namespace App\Repositories;

use App\Models\ArticleMouvement;

class ArticleMouvementRepository extends BaseRepository
{
    public function __construct(ArticleMouvement $articleMouvement)
    {
        parent::__construct($articleMouvement);
    }
}
