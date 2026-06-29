<?php

namespace App\Repositories;

use App\Models\VoyageMarchandise;
use Illuminate\Database\Eloquent\Model;

class VoyageMarchandiseRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(VoyageMarchandise $voyageMarchandise)
    {
        $this->model = $voyageMarchandise;
        parent::__construct($voyageMarchandise);
    }


}