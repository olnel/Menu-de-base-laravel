<?php

namespace App\Repositories;

use App\Models\ArticleMagasin;

class ArticlemagasinRepository extends BaseRepository
{
    public function __construct(ArticleMagasin $articleMagasin)
    {
        parent::__construct($articleMagasin);
    }


}
