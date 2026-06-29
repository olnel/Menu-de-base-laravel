<?php

namespace App\Repositories;

use App\Models\CarburantMouvement;
use App\Utils\BaseQueryTemplate;
use Illuminate\Database\Eloquent\Model;

class CarburantMouvementRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(CarburantMouvement $carburantMouvement)
    {
        $this->model = $carburantMouvement;
        parent::__construct($carburantMouvement);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getMouvementParType(array $filters = [], array $scopes = [])
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);
        return $query->select('type')
            ->selectRaw('COUNT(*) as count, SUM(montant_mvmt) as total_montant')
            ->groupBy('type')
            ->get();
    }
}
