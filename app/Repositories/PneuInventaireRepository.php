<?php

namespace App\Repositories;

use App\Models\PneuInventaire;
use Illuminate\Database\Eloquent\Model;

class PneuInventaireRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(PneuInventaire $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }
}
