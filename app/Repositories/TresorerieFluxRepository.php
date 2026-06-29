<?php

namespace App\Repositories;

use App\Models\TresorerieFlux;
use App\Utils\BaseQueryTemplate;

class TresorerieFluxRepository extends BaseRepository
{
    public function __construct(protected TresorerieFlux $tresorerieFlux)
    {
        parent::__construct($tresorerieFlux);
    }

    public function queryTotal(array $filters = [], array $scopes = [])
    {
        // Base query filtrée pour les flux
        $baseQuery = BaseQueryTemplate::apply($this->tresorerieFlux->newQuery(), $filters, $scopes);

        // Sous-requête pour récupérer le dernier flux par trésorerie
        $lastFluxSubquery = $baseQuery
            ->selectRaw('MAX(tresorerie_fluxes.id) as last_id, tresorerie_id')
            ->groupBy('tresorerie_id');

        // Solde actuel par trésorerie : on part de la table tresoreries pour inclure toutes les trésoreries
        $soldeParTresorerie = \DB::table('tresoreries as t')
            ->whereNull('t.deleted_at')
            ->leftJoinSub($lastFluxSubquery, 'last_flux', function($join) {
                $join->on('t.id', '=', 'last_flux.tresorerie_id');
            })
            ->leftJoin('tresorerie_fluxes as tf', 'tf.id', '=', 'last_flux.last_id')
            ->selectRaw('t.id as tresorerie_id, t.nom_tresorerie, COALESCE(tf.solde_final, 0) as solde_actuel')
            ->get();

        // Total général de tous les soldes
        $totalGeneral = $soldeParTresorerie->sum('solde_actuel');

        // Totaux entrées/sorties par trésorerie (filtrés)
        $totauxParTresorerie = $baseQuery
            ->join('tresoreries as t', 't.id', '=', 'tresorerie_fluxes.tresorerie_id')
            ->whereNull('t.deleted_at')
            ->selectRaw('tresorerie_fluxes.tresorerie_id, t.nom_tresorerie,
                     SUM(CASE WHEN tresorerie_fluxes.type_mvt = "ENTREE" THEN tresorerie_fluxes.montant ELSE 0 END) as total_entrees,
                     SUM(CASE WHEN tresorerie_fluxes.type_mvt = "SORTIE" THEN tresorerie_fluxes.montant ELSE 0 END) as total_sorties')
            ->groupBy('tresorerie_fluxes.tresorerie_id', 't.nom_tresorerie')
            ->get();

        return [
            'total_general'        => $totalGeneral,
            'solde_par_tresorerie' => $soldeParTresorerie,
            'totaux_par_tresorerie'=> $totauxParTresorerie
        ];
    }





}
