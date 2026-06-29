<?php

namespace App\Repositories;

use App\Models\CarburantCard;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Model;

class CarburantCardRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(CarburantCard $carburantcard)
    {
        $this->model = $carburantcard;
        parent::__construct($carburantcard);
    }
}