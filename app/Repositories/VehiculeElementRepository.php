<?php

namespace App\Repositories;

use App\Models\VehiculeElement;
use Illuminate\Database\Eloquent\Model;
class VehiculeElementRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(VehiculeElement $vehiculeElement)
    {
        $this->model = $vehiculeElement;
        parent::__construct($vehiculeElement);
    }

    public function deleteByElementId(int $elementId)
    {
        return $this->model->where('vehicule_id', $elementId)->delete();
    }

    /**
     * @param int $vehiculeID
     * @return mixed
     */
    public function getElementsByVehicule(int $vehiculeID)
    {
        return $this->model->where('vehicule_id', $vehiculeID)->with('pneuSerie')->get();
    }

    public function fetchDistinctByColumn()
    {
        $column = 'emplacement';
        return $this->model
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->get()
            ->pluck($column)
            ->filter()
            ->values();
    }
}
