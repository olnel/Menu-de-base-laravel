<?php

namespace App\Services;

use App\Services\Base\BaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\CarburantCardService;
use App\Services\CarburantMouvementService;
use App\Services\CarburantTransactionService;
use App\Services\FactureClientService;
use App\Services\RemorqueService;
use App\Services\ReservationService;
use App\Services\TresorerieFluxService;
use App\Services\VehiculeService;
use App\Services\VoyageService;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getVoyageReservationByDestination(array $filtre): array
    {
        $voyage = app(VoyageService::class)->dashboard($filtre);
        $reservation = app(ReservationService::class)->dashboard($filtre);
        $list_destination = $this->traiteDestination($voyage, $reservation);
        $listClient = $this->traiteClient($voyage, $reservation);

        return [
            "recap_voyage" => $voyage,
            "recap_reservation" => $reservation,
            "destinations" => $list_destination,
            "clients" => $listClient,
        ];
    }

    public function traiteDestination($voyage, $reservation): Collection
    {
        $destinationVoyage = $voyage['listeDestination'];
        $destinationReservation = $reservation['destination'];

        $voyageByDestination = $destinationVoyage->keyBy('destination');
        $reservationByDestination = $destinationReservation->keyBy('destination');

        $allDestinations = $voyageByDestination->keys()
            ->merge($reservationByDestination->keys())
            ->unique();

        return $allDestinations->map(function ($client) use ($voyageByDestination, $reservationByDestination) {
            return [
                'destination' => $client,
                'count_reservation' => $reservationByDestination[$client]['count'] ?? 0,
                'count_voyage' => $voyageByDestination[$client]['count'] ?? 0,
            ];
        })->values();
    }

    private function traiteClient($voyage, $reservation)
    {

        $clientVoyage = $voyage['client'];
        $clientReservation = $reservation['client'];

        $voyageByClient = $clientVoyage->keyBy('nom_client');
        $reservationByClient = $clientReservation->keyBy('client_name');

        $allData = $voyageByClient->keys()
            ->merge($reservationByClient->keys())
            ->unique();

        return $allData->map(function ($destination) use ($voyageByClient, $reservationByClient) {
            return [
                'nom_client' => $destination,
                'count_reservation' => $reservationByClient[$destination]['count'] ?? 0,
                'count_voyage' => $voyageByClient[$destination]['count'] ?? 0,
            ];
        })->values();
    }

    public function dashboardComptabilite($filtre)
    {
        $flux = app(TresorerieFluxService::class)->dasboard(filtre: $filtre);
        $factures = app(FactureClientService::class)->dashboard($filtre);
        $approvisionnements = app(ArticleApprovisionnementService::class)->dashboard($filtre);

        return array_merge($flux, [
            'comptes_a_recevoir' => [
                'total' => $factures['totaux']['montant_reste_paye'] ?? 0,
                'details' => $factures['count_par_statut_facture'] ?? []
            ],
            'comptes_a_payer' => [
                'total' => $approvisionnements['total_reste_a_payer'] ?? 0,
                'total_ttc' => $approvisionnements['total_ttc'] ?? 0,
                'total_paye' => $approvisionnements['total_paye'] ?? 0
            ],
            'etat_financier' => $this->getFinancialReportDetails($filtre)
        ]);
    }

    public function getFinancialReportDetails(array $filtre): array
    {
        // Comptes à recevoir (Factures Clients non soldées)
        $recevables = \App\Models\FactureClient::with('client')
            ->where('montant_reste_a_payer', '>', 0)
            ->whereIn('statut_facture', ['envoyée', 'partiellement payée'])
            ->get()
            ->groupBy('client_id')
            ->map(function ($factures) {
                $client = $factures->first()->client;
                return [
                    'nom' => $client->nom_client ?? 'Client Inconnu',
                    'total_du' => $factures->sum('montant_ttc'),
                    'total_paye' => $factures->sum('montant_payer'),
                    'solde' => $factures->sum('montant_reste_a_payer'),
                    'factures' => $factures->map(fn($f) => [
                        'numero' => $f->numero_facture,
                        'date' => $f->date_facture,
                        'montant_ttc' => $f->montant_ttc,
                        'solde' => $f->montant_reste_a_payer
                    ])
                ];
            })->values();

        // Comptes à payer (Approvisionnements non soldés)
        $payables = \App\Models\ArticleApprovisionnement::with('fournisseur')
            ->where('montant_reste_a_payer_appro', '>', 0)
            ->get()
            ->groupBy('fournisseur_id')
            ->map(function ($appros) {
                $fournisseur = $appros->first()->fournisseur;
                return [
                    'nom' => $fournisseur->nom_fournisseur ?? 'Fournisseur Inconnu',
                    'total_du' => $appros->sum('montant_ttc_appro'),
                    'total_paye' => $appros->sum('montant_payer_appro'),
                    'solde' => $appros->sum('montant_reste_a_payer_appro'),
                    'approvisionnements' => $appros->map(fn($a) => [
                        'numero' => $a->numero_bon_commande ?? $a->id,
                        'date' => $a->date_appro,
                        'montant_ttc' => $a->montant_ttc_appro,
                        'solde' => $a->montant_reste_a_payer_appro
                    ])
                ];
            })->values();

        return [
            'recevables' => $recevables,
            'payables' => $payables,
        ];
    }

    public function dashboardFactureClient($filtre)
    {
        return app(FactureClientService::class)->dashboard($filtre);
    }

    public function dashboardVehicule($filter)
    {
        $totalVehicule = app(VehiculeService::class)->getTotalVehicule([]);
        $totalRemorque = app(RemorqueService::class)->getTotalRemorque([]);
        $vehiculeStats = app(VehiculeService::class)->getVehiculeStat($filter);
        $remorqueStats = app(RemorqueService::class)->getRemorqueData($filter);
        return [
            'totalVehicule' => $totalVehicule,
            'totalRemorque' => $totalRemorque,
            'vehiculeStats' => $vehiculeStats,
            'remorqueStats' => $remorqueStats,
        ];
    }
    public function dashboardPneu(array $filtre): array
    {
        $parseDate = fn(?string $d): ?Carbon => $d ? (preg_match('/^\d{2}-\d{2}-\d{4}$/', $d) ? Carbon::createFromFormat('d-m-Y', $d) : Carbon::parse($d)) : null;

        $start = $parseDate($filtre['start_date'] ?? null);
        $end   = $parseDate($filtre['end_date']   ?? null);

        // ── KPIs ─────────────────────────────────────────────────────────────
        $totalEnService = DB::table('pneu_series')->whereNull('deleted_at')
            ->where(fn($q) => $q->whereNotNull('vehicule_id')->orWhereNotNull('remorque_id'))
            ->count();

        $totalEnStock = DB::table('pneu_series')->whereNull('deleted_at')
            ->whereNull('vehicule_id')->whereNull('remorque_id')
            ->count();

        $totalRemplacements = DB::table('pneu_remplacements')->whereNull('deleted_at')
            ->when($start, fn($q) => $q->where('date_operation', '>=', $start->toDateString()))
            ->when($end,   fn($q) => $q->where('date_operation', '<=', $end->toDateString()))
            ->count();

        $pneusASurveiller = DB::table('pneu_series')->whereNull('deleted_at')
            ->whereIn('etat', ['USÉ', 'RECHAPÉ'])
            ->count();

        // ── Distribution par état ─────────────────────────────────────────────
        $distributionEtat = DB::table('pneu_series')->whereNull('deleted_at')
            ->select('etat', DB::raw('count(*) as count'))
            ->whereNotNull('etat')
            ->groupBy('etat')
            ->orderByDesc('count')
            ->get()
            ->map(fn($r) => ['label' => $r->etat, 'value' => (int) $r->count])
            ->values()->toArray();

        // ── Top pneus par voyages (actifs seulement) ──────────────────────────
        $topPneus = DB::table('pneu_series as ps')->whereNull('ps.deleted_at')
            ->leftJoin('voyage_pneus as vp', fn($j) => $j->on('ps.id', '=', 'vp.pneu_serie_id')->where('vp.is_secours', false))
            ->select('ps.numero_serie', 'ps.etat', 'ps.position_actuel', DB::raw('COUNT(vp.id) as voyage_count'))
            ->groupBy('ps.id', 'ps.numero_serie', 'ps.etat', 'ps.position_actuel')
            ->orderByDesc('voyage_count')
            ->limit(20)
            ->get()
            ->map(fn($r) => [
                'label'    => $r->numero_serie ?? '—',
                'value'    => (int) $r->voyage_count,
                'etat'     => $r->etat,
                'position' => $r->position_actuel,
            ])->toArray();

        // ── Remplacements 12 derniers mois ────────────────────────────────────
        $remplacementsParMois = DB::table('pneu_remplacements')->whereNull('deleted_at')
            ->where('date_operation', '>=', Carbon::now()->subMonths(11)->startOfMonth()->toDateString())
            ->select(DB::raw("DATE_FORMAT(date_operation, '%Y-%m') as mois"), DB::raw('count(*) as count'))
            ->groupBy('mois')->orderBy('mois')
            ->get()
            ->map(fn($r) => ['label' => $r->mois, 'value' => (int) $r->count])
            ->toArray();

        // ── Distribution remplacement vs permutation ───────────────────────────
        $distributionTypeOp = DB::table('pneu_remplacements')->whereNull('deleted_at')
            ->when($start, fn($q) => $q->where('date_operation', '>=', $start->toDateString()))
            ->when($end,   fn($q) => $q->where('date_operation', '<=', $end->toDateString()))
            ->select('type_operation', DB::raw('count(*) as count'))
            ->groupBy('type_operation')
            ->get()
            ->map(fn($r) => ['label' => ucfirst($r->type_operation), 'value' => (int) $r->count])
            ->toArray();

        // ── Dernières opérations (15) ─────────────────────────────────────────
        $derniersOps = DB::table('pneu_remplacements as pr')->whereNull('pr.deleted_at')
            ->leftJoin('pneu_series as retire', 'pr.pneu_serie_retire_id', '=', 'retire.id')
            ->leftJoin('pneu_series as monte',  'pr.pneu_serie_monte_id',  '=', 'monte.id')
            ->leftJoin('vehicules as v', 'pr.vehicule_id', '=', 'v.id')
            ->leftJoin('remorques as r', 'pr.remorque_id', '=', 'r.id')
            ->select(
                'pr.id', 'pr.type_operation', 'pr.date_operation', 'pr.position_retire', 'pr.motif',
                'retire.numero_serie as pneu_retire', 'retire.etat as etat_retire',
                'monte.numero_serie as pneu_monte',
                'v.immatriculation', 'r.numero_remorque'
            )
            ->when($start, fn($q) => $q->where('pr.date_operation', '>=', $start->toDateString()))
            ->when($end,   fn($q) => $q->where('pr.date_operation', '<=', $end->toDateString()))
            ->orderByDesc('pr.date_operation')
            ->limit(15)
            ->get()
            ->map(fn($r) => (array) $r)
            ->toArray();

        return [
            'kpi'                    => compact('totalEnService', 'totalEnStock', 'totalRemplacements', 'pneusASurveiller'),
            'distribution_etat'      => $distributionEtat,
            'top_pneus'              => $topPneus,
            'remplacements_par_mois' => $remplacementsParMois,
            'distribution_type_op'   => $distributionTypeOp,
            'derniers_ops'           => $derniersOps,
        ];
    }

    public function dashboardChauffeur(array $filtre): array
    {
        $parseDate = fn(?string $d): ?Carbon => $d
            ? (preg_match('/^\d{2}-\d{2}-\d{4}$/', $d) ? Carbon::createFromFormat('d-m-Y', $d) : Carbon::parse($d))
            : null;

        $start = $parseDate($filtre['start_date'] ?? null);
        $end   = $parseDate($filtre['end_date']   ?? null);

        $rows = DB::table('voyages')
            ->join('chauffeurs', 'voyages.chauffeur_id', '=', 'chauffeurs.id')
            ->whereNull('voyages.deleted_at')
            ->whereNotNull('voyages.chauffeur_id')
            ->where('chauffeurs.is_aide_chauffeur', false)
            ->when($start, fn($q) => $q->where('voyages.date_voyage', '>=', $start->toDateString()))
            ->when($end,   fn($q) => $q->where('voyages.date_voyage', '<=', $end->toDateString()))
            ->select([
                'voyages.chauffeur_id',
                DB::raw("CONCAT(UPPER(chauffeurs.nom), ' ', chauffeurs.prenom) as nom_chauffeur"),
                DB::raw('SUM(COALESCE(voyages.heures_facturables, 0)) as total_heures_facturables'),
                DB::raw('SUM(COALESCE(voyages.heures_non_facturables, 0)) as total_heures_non_facturables'),
                DB::raw('SUM(COALESCE(voyages.heures_facturables, 0) + COALESCE(voyages.heures_non_facturables, 0)) as total_heures'),
                DB::raw('SUM(COALESCE(voyages.km_parcouru, 0)) as total_km'),
                DB::raw('COUNT(voyages.id) as total_voyages'),
            ])
            ->groupBy('voyages.chauffeur_id', 'chauffeurs.nom', 'chauffeurs.prenom')
            ->orderByDesc('total_heures')
            ->get()
            ->map(fn($r) => (array) $r);

        $kpi = [
            'total_heures'                 => round($rows->sum('total_heures'), 1),
            'total_heures_facturables'     => round($rows->sum('total_heures_facturables'), 1),
            'total_heures_non_facturables' => round($rows->sum('total_heures_non_facturables'), 1),
            'total_km'                     => (int) $rows->sum('total_km'),
            'total_voyages'                => (int) $rows->sum('total_voyages'),
            'nb_chauffeurs'                => $rows->count(),
        ];

        $mensuel = DB::table('voyages')
            ->join('chauffeurs', 'voyages.chauffeur_id', '=', 'chauffeurs.id')
            ->whereNull('voyages.deleted_at')
            ->whereNotNull('voyages.chauffeur_id')
            ->where('chauffeurs.is_aide_chauffeur', false)
            ->where('voyages.date_voyage', '>=', Carbon::now()->subMonths(11)->startOfMonth()->toDateString())
            ->select([
                DB::raw("DATE_FORMAT(voyages.date_voyage, '%Y-%m') as label"),
                DB::raw('SUM(COALESCE(voyages.heures_facturables, 0) + COALESCE(voyages.heures_non_facturables, 0)) as value'),
            ])
            ->groupBy('label')
            ->orderBy('label')
            ->get()
            ->map(fn($r) => ['label' => $r->label, 'value' => round($r->value, 1)])
            ->toArray();

        return [
            'kpi'          => $kpi,
            'par_chauffeur' => $rows->values()->toArray(),
            'mensuel'      => $mensuel,
        ];
    }

    public function dahsboardCarburant(array $filtre): array
    {
        $recapCarburantCard = app(CarburantCardService::class)->getTotal([]);
        $recapCarburantTransaction = app(CarburantTransactionService::class)->getTotal([]);
        $transactionByType = app(CarburantTransactionService::class)->getTransactionType($filtre);
        $mouvementByType = app(CarburantMouvementService::class)->getMouvementByType($filtre);
        $topVehiculesByLitres = app(CarburantTransactionService::class)->getTopVehiculeForCarburant($filtre);
        $topCardsByTransactions = app(CarburantTransactionService::class)->getTopCardsByTransactions($filtre);

        return [
            'totalCard' => $recapCarburantCard,
            'totalCarbtrans' => $recapCarburantTransaction,
            'transactionBytype' => $transactionByType,
            'mouvementByType' => $mouvementByType,
            'topVehiculesByLitres' => $topVehiculesByLitres,
            'topCardsByTransactions' => $topCardsByTransactions,
        ];
    }
}
