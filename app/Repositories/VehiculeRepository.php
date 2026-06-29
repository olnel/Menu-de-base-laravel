<?php

namespace App\Repositories;
use App\Utils\BaseQueryTemplate;
use App\Models\Vehicule;

class VehiculeRepository extends BaseRepository
{
    public function __construct(Vehicule $model)
    {
        parent::__construct($model);
    }

    public function find(int $vehiculeId)
    {
        return $this->model->with(['element_vehicules'])
            ->find($vehiculeId);
    }

    public function fetchDistinctByColumn(string $column = 'couleur')
    {

        return $this->model
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->get()
            ->pluck($column)
            ->filter()
            ->values();
    }

    /**
     * fonction permet de chercher un élément
     * @param array $critere
     * @return mixed
     */
    public function findElement(array $critere)
    {
        return $this->model->where($critere)->first();
    }

    //Recuperer les vehicules avec le nbr-voyage et kilometrage total
    public function getVehiculeStats(array $filters = [], array $scopes = [])
    {
        $apply = fn($q) => BaseQueryTemplate::apply($q, $filters, $scopes);
        $vehicules = $this->model::query()
            ->withCount(['voyages as nbr_voyage' => $apply])
            //=> fn ($q) => $apply($q, ['date_start','date_end'])
            ->withSum(['voyages as total_kilometrage' => $apply], 'kilometrage')
            ->orderByDesc('nbr_voyage');

        return $vehicules->get()->map(fn($row) =>
            [
                'id' => (int) $row->id,
                'immatriculation' => $row->immatriculation,
                'label' => $row->immatriculation,
                'nbr_voyage' => (int) ($row->nbr_voyage ?? 0),
                'total_kilometrage' => (int) ($row->total_kilometrage ?? 0),
            ]
        );
    }

}
