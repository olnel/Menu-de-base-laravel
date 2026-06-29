<?php

namespace App\Repositories;

use App\Models\PosteDepense;

class PosteDepenseRepository extends BaseRepository
{
    public function __construct(PosteDepense $posteDepense)
    {
        parent::__construct($posteDepense);
    }
}
