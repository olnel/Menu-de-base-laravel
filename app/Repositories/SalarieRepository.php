<?php

namespace App\Repositories;

use App\Models\Salarie;
use Illuminate\Database\Eloquent\Model;

class SalarieRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Salarie $salarie)
    {
        $this->model = $salarie;
        parent::__construct($salarie);
    }

    public function findLastSalarie()
    {
        return $this->model->withTrashed()->orderBy('count_matricule', 'desc')->first();
    }
}
