<?php

namespace App\Repositories;

use App\Models\VoyageCharge;
use Illuminate\Database\Eloquent\Model;
class VoyageChargeRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(VoyageCharge $voyageCharge)
    {
        $this->model = $voyageCharge;
        parent::__construct($voyageCharge);
    }
}
