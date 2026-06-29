<?php

namespace App\Repositories;

use App\Models\ArticleFamille;
use Illuminate\Database\Eloquent\Model;

class ArticleFamilleRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(ArticleFamille $articleFamille)
    {
        $this->model = $articleFamille;
        parent::__construct($articleFamille);
    }

    public function findElement(array $critere)
    {
        return $this->model->where($critere)->first();
    }
}
