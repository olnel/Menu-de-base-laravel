<?php

namespace App\Repositories;

use App\Models\VoyageAffectation;
use Illuminate\Database\Eloquent\Model;

class VoyageAffectationRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(VoyageAffectation $voyageMarchandise)
    {
        $this->model = $voyageMarchandise;
        parent::__construct($voyageMarchandise);
    }
}
