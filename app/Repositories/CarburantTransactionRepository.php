<?php

namespace App\Repositories;


use App\Models\CarburantTransaction;
use App\Utils\BaseQueryTemplate;
use Illuminate\Database\Eloquent\Model;

class CarburantTransactionRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(CarburantTransaction $categorie)
    {
        $this->model = $categorie;
        parent::__construct($categorie);
    }
    public function getFreqStation($filter)
    {
        return [];
    }

    public function getTransactionParType(array $filters = [], array $scopes = [])
    {
        $query = $this->model->query();
        return $query->select('type')
            ->selectRaw('COUNT(*) as count, SUM(montant) as total')
            ->groupBy('type')
            ->get();
    }

    public function getVehiculeByLitresTransaction(array $filters = [], array $scopes = [])
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);

        return $query
            ->leftJoin('vehicules', 'carburant_transactions.vehicule_id', '=', 'vehicules.id')
            ->selectRaw('carburant_transactions.vehicule_id as vehicule_id')
            ->selectRaw('MAX(vehicules.immatriculation) as label')
            ->selectRaw('SUM(carburant_transactions.carburant_litre) as total_litres')
            ->selectRaw('COUNT(*) as total_tx')
            ->groupBy('carburant_transactions.vehicule_id')
            ->orderByDesc('total_litres')
            ->get();
    }

    public function getTopCardsByTransactions(array $filters = [], array $scopes = [])
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);

        return $query
            ->leftJoin('carburant_cards', 'carburant_transactions.carburant_card_id', '=', 'carburant_cards.id')
            ->whereNotNull('carburant_transactions.carburant_card_id')
            ->selectRaw('carburant_transactions.carburant_card_id as carburant_card_id')
            ->selectRaw('MAX(carburant_cards.numero_carte) as label')
            ->selectRaw('COUNT(*) as total_tx')
            ->groupBy('carburant_transactions.carburant_card_id')
            ->orderByDesc('total_tx')
            ->get();
    }
}
