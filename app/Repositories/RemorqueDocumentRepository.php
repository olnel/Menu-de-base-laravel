<?php

namespace App\Repositories;

use App\Models\RemorqueDocument;
use Illuminate\Database\Eloquent\Model;

class RemorqueDocumentRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(RemorqueDocument $remorqueDocument)
    {
        $this->model = $remorqueDocument;
        parent::__construct($remorqueDocument);
    }

    public function deleteByElementId(int $elementId)
    {
        return $this->model->where('remorque_id', $elementId)->delete();
    }

    /**
     * @param int $vehiculeID
     * @return mixed
     */
    public function getDocumentByVehicule(int $vehiculeID)
    {
        return $this->model->where('remorque_id', $vehiculeID)
            ->orderBy('date_expiration', 'desc')
            ->get();
    }
}
