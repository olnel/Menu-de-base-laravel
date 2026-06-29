<?php

namespace App\Repositories;

use App\Models\Remorque;
use App\Utils\BaseQueryTemplate;

class RemorqueRepository extends BaseRepository
{
    public function __construct(Remorque $model)
    {
        parent::__construct($model);
    }

    public function find(int $vehiculeId)
    {
        return $this->model->with(['element_remorques'])
            ->find($vehiculeId);
    }

    public function fetchDistinctByColumn(string $column = 'numero_remorque')
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



    //Recuperer les remorques avec le nbr-voyage
    public function getRemorqueStats(array $filters = [], array $scopes = [])
    {
        $applyFilters = fn($q) => BaseQueryTemplate::apply($q, $filters, $scopes);
        //=> fn ($q) => $apply($q, ['date_start','date_end'])
        $query = $this->model::query()
            ->withCount(['voyages as nbr_voyage'=>$applyFilters])
            ->orderByDesc('nbr_voyage');

        return $query->get()->map(fn($row) => [
            'id' => (int) $row->id,
            'numero_remorque' => $row->numero_remorque,
            'nbr_voyage' => (int) ($row->nbr_voyage ?? 0),
        ]);
    }

}
