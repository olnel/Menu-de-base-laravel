<?php

namespace App\Repositories;

use App\Models\PneuRemplacement;
use Illuminate\Database\Eloquent\Model;

class PneuRemplacementRepository extends BaseRepository
{
    protected Model $model;

    protected string $orderByColumn    = 'date_operation';
    protected string $orderByDirection = 'desc';

    public function __construct(PneuRemplacement $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getByVehicule(int $vehiculeId)
    {
        return $this->model
            ->where('vehicule_id', $vehiculeId)
            ->with(['pneuSerieRetire', 'pneuSerieMonte', 'user'])
            ->orderByDesc('date_operation')
            ->get();
    }

    public function getByPneuSerie(int $pneuSerieId)
    {
        return $this->model
            ->where('pneu_serie_retire_id', $pneuSerieId)
            ->orWhere('pneu_serie_monte_id', $pneuSerieId)
            ->with(['vehicule', 'remorque', 'pneuSerieRetire', 'pneuSerieMonte', 'user'])
            ->orderByDesc('date_operation')
            ->get();
    }
}
