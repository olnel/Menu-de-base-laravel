<?php

namespace App\Repositories;
use App\Repositories\BaseRepository;
use App\Models\Reservation;
use App\Utils\BaseQueryTemplate;
use Illuminate\Database\Eloquent\Model;

class ReservationRepository extends BaseRepository
{
    protected Model $model;
    protected string $orderByColumn    = 'date_reservation';
    protected string $orderByDirection = 'desc';

    public function __construct(Reservation $reservation)
    {
        $this->model = $reservation;
        parent::__construct($reservation);
    }

    public function findLastReservation()
    {
        return $this->model
            ->orderBy('count_numero_reservation', 'DESC')
            ->first();
    }

    public function find($reservationId)
    {
        return $this->model->find($reservationId);
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

    public function getReservationParDestination(array $filters= [], array $scopes = [])
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);
        return $query->select('lieu_livraison as destination')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('destination')
            ->get();
    }

    public function recapVoyageClient(array $filters= [], array $scopes = [])
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);

        return $query->join('clients', 'reservations.client_id', '=', 'clients.id')
            ->select('clients.nom_client as client_name')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('clients.nom_client')
            ->get();
    }
}
