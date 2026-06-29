<?php

namespace App\Repositories;

use App\Models\ParamElementDetail;
use Illuminate\Database\Eloquent\Model;

class ParamElementDetailRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(ParamElementDetail $paramElementDetail)
    {
        $this->model = $paramElementDetail;
        parent::__construct($paramElementDetail);
    }

    public function deleteByElementId(int $elementId): int
    {
        return $this->model->where('param_element_id', $elementId)->delete();
    }
}
