<?php

namespace App\Repositories;

use App\Models\FactureClient;
use App\Utils\BaseQueryTemplate;
use Illuminate\Database\Eloquent\Model;

class FactureClientRepository extends BaseRepository
{

    protected Model $model;
    public function __construct(FactureClient $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function find(string $firstDate, string $lastDate)
    {
        return $this->model
            ->whereBetween('date_facture', [$firstDate, $lastDate])
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function recapFactureCliffent(array $filters, array $scopes)
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);

        // Total factures
        $count_facture = (clone $query)->count();

        // Récupération du nombre de factures par statut présentes en BDD
        $counts = (clone $query)
            ->select('statut_facture')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('statut_facture')
            ->pluck('count', 'statut_facture')
            ->toArray();

        // Tous les statuts possibles
        $allStatus = ['Brouillon', 'envoyée', 'partiellement payée', 'payée'];

        // Générer le résultat final : si le statut n'existe pas dans la BDD, mettre 0
        $cout_par_statut_facture = [];
        foreach ($allStatus as $status) {
            $cout_par_statut_facture[$status] = $counts[$status] ?? 0;
        }

        return [
            'count_facture' => $count_facture,
            'count_par_statut_facture' => $cout_par_statut_facture,
            'totaux' => $this->totalMontantFacture(clone $query)
        ];
    }

    private function totalMontantFacture($query)
    {
        $totaux = $query->selectRaw('
            SUM(montant_ttc) as total_ttc,
            SUM(CASE WHEN statut_facture = "partiellement payée" THEN montant_payer ELSE 0 END) as total_partiellement_paye,
            SUM(CASE WHEN statut_facture = "payée" THEN montant_payer ELSE 0 END) as total_paye
        ')->first();

        return [
            'total_ttc' => $totaux->total_ttc ?? 0,
            'total_partiellement_paye' => $totaux->total_partiellement_paye ?? 0,
            'total_paye' => $totaux->total_paye ?? 0,
            'montant_reste_paye' => ($totaux->total_ttc ?? 0) - (($totaux->total_partiellement_paye ?? 0) + ($totaux->total_paye ?? 0)),
        ];
    }


}
