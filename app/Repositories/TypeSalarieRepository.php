<?php

namespace App\Repositories;

use App\Models\TypeSalarie;
use Illuminate\Database\Eloquent\Model;

class TypeSalarieRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(TypeSalarie $typeSalarie)
    {
        $this->model = $typeSalarie;
        parent::__construct($typeSalarie);
    }
}
