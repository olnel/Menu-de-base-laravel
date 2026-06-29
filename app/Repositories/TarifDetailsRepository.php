<?php

namespace App\Repositories;

use App\Models\TarifDetail;

class TarifDetailsRepository extends BaseRepository
{
    public function __construct(TarifDetail $tarifDetail)
    {
        parent::__construct($tarifDetail);
    }

    public function deleteByTarifId(mixed $id)
    {
        try {
            return $this->model->where('tarif_id', $id)->delete();
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return false;
        }
    }
}
