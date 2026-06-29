<?php

namespace App\Repositories;

use App\Models\Voyage;
use App\Utils\BaseQueryTemplate;
use Illuminate\Database\Eloquent\Model;

class VoyageRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Voyage $voyage)
    {
        $this->model = $voyage;
        parent::__construct($voyage);
    }

    public function findLastVoyage(string $firstDate, string $lastDate)
    {
        return $this->model
            ->whereBetween('date_voyage', [$firstDate, $lastDate])
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function fetchDistinctByColumn(string $column)
    {
        return $this->model
            ->select($column)
            ->distinct()
            ->whereNotNull($column)
            ->orderBy($column)
            ->pluck($column)
            ->filter()
            ->values();
    }

    public function getVoyageParDestination(array $filters= [], array $scopes = [])
    {

        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);
        return $query->select('destination')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('destination')
            ->get();
    }

    public function recapVoyageClient(array $filters= [], array $scopes = [])
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);

        return $query->join('reservations', 'voyages.reservation_id', '=', 'reservations.id')
            ->join('clients', 'reservations.client_id', '=', 'clients.id')
            ->select('clients.nom_client as nom_client')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('clients.nom_client')
            ->get();
    }


    public function getVoyagesParAnnee(array $filters = [], array $scopes = [])
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);

        return $query->selectRaw('YEAR(date_voyage) as annee, MONTH(date_voyage) as mois, COUNT(*) as total_voyages')
            ->groupByRaw('YEAR(date_voyage), MONTH(date_voyage)')
            ->orderByRaw('YEAR(date_voyage), MONTH(date_voyage)')
            ->get()
            ->groupBy('annee')
            ->map(function ($items, $annee) {
                $moisLabels = [
                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                ];
                $moisData = [];
                for ($i = 1; $i <= 12; $i++) {
                    $moisData[$moisLabels[$i]] = 0;
                }
                foreach ($items as $item) {
                    $moisData[$moisLabels[$item->mois]] = $item->total_voyages;
                }
                return [
                    'annee' => $annee,
                    'data' => ($moisData)
                ];
            })
            ->values();
    }


}
