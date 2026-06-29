<?php

namespace App\Repositories;


use App\Models\Categorie;
use Illuminate\Database\Eloquent\Model;

class CategorieRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(Categorie $categorie)
    {
        $this->model = $categorie;
        parent::__construct($categorie);
    }
}
