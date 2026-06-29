<?php

namespace App\Repositories;

use App\Models\VehiculeDocument;
use App\Models\VehiculeElement;
use Illuminate\Database\Eloquent\Model;

class VehiculeDocumentRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(VehiculeDocument $vehiculeDocument)
    {
        $this->model = $vehiculeDocument;
        parent::__construct($vehiculeDocument);
    }

    public function deleteByElementId(int $elementId)
    {
        return $this->model->where('vehicule_id', $elementId)->delete();
    }

    /**
     * @param int $vehiculeID
     * @return mixed
     */
    public function getDocumentByVehicule(int $vehiculeID)
    {
        return $this->model->where('vehicule_id', $vehiculeID)
            ->orderBy('date_expiration', 'desc')
            ->get();
    }
}
