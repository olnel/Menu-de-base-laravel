<?php

namespace App\Services;

use App\Models\Vehicule;
use App\Models\Remorque;
use App\Models\Voyage;
use App\Models\ReparationVehicule;
use App\Models\VoyageCharge;
use App\Models\Paie;
use App\Models\Salarie;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RentabiliteService
{
    /**
     * Get paginated profitability data.
     */
    public function getPaginatedProfitability(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        // 1. Get all matching assets
        $vehicules = Vehicule::query()
            ->when($search, function ($q) use ($search) {
                $q->where('immatriculation', 'LIKE', "%$search%")
                    ->orWhere('marque', 'LIKE', "%$search%")
                    ->orWhere('modele', 'LIKE', "%$search%");
            })
            ->get()
            ->map(fn($v) => ['id' => $v->id, 'type' => 'vehicule', 'model' => $v, 'label' => $v->immatriculation]);

        $remorques = Remorque::query()
            ->when($search, function ($q) use ($search) {
                $q->where('numero_remorque', 'LIKE', "%$search%")
                    ->orWhere('marque_remorque', 'LIKE', "%$search%");
            })
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'type' => 'remorque', 'model' => $r, 'label' => $r->numero_remorque]);

        $allAssets = $vehicules->concat($remorques);

        // 2. Perform calculations for ALL matching assets (to allow global sorting)
        // Optimization: Bulk fetch aggregations instead of one by one
        $results = $this->calculateBulkStats($allAssets, $startDate, $endDate);

        // 3. Sort by Marge Nette by default
        $sortedResults = $results->sortByDesc('marge_nette')->values();

        // 4. Paginate the collection
        return new LengthAwarePaginator(
            $sortedResults->forPage($page, $perPage)->values(),
            $sortedResults->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Calculate stats for a collection of assets in a more optimized way.
     */
    private function calculateBulkStats(Collection $assets, $startDate, $endDate): Collection
    {
        $vehiculeIds = $assets->where('type', 'vehicule')->pluck('id');
        $remorqueIds = $assets->where('type', 'remorque')->pluck('id');

        // Aggregated Revenues & Nb Voyages
        $voyageStatsVehicule = $this->getAggregatedVoyages($vehiculeIds, 'vehicule_id', $startDate, $endDate);
        $voyageStatsRemorque = $this->getAggregatedVoyages($remorqueIds, 'remorque_id', $startDate, $endDate);

        // Aggregated Maintenance
        $maintenanceStatsVehicule = $this->getAggregatedMaintenance($vehiculeIds, 'vehicule_id', $startDate, $endDate);
        $maintenanceStatsRemorque = $this->getAggregatedMaintenance($remorqueIds, 'remorque_id', $startDate, $endDate);

        // Aggregated Route Charges
        $chargesStatsVehicule = $this->getAggregatedCharges($vehiculeIds, 'vehicule_id', $startDate, $endDate);
        $chargesStatsRemorque = $this->getAggregatedCharges($remorqueIds, 'remorque_id', $startDate, $endDate);

        // For Social Costs, we still need a bit of a loop, but let's optimize it
        $socialCostsVehicule = $this->getBulkSocialCosts($vehiculeIds, $startDate, $endDate);

        return $assets->map(function ($item) use (
            $voyageStatsVehicule, $voyageStatsRemorque,
            $maintenanceStatsVehicule, $maintenanceStatsRemorque,
            $chargesStatsVehicule, $chargesStatsRemorque,
            $socialCostsVehicule
        ) {
            $id = $item['id'];
            $type = $item['type'];
            $asset = $item['model'];

            $voyage = ($type === 'vehicule' ? $voyageStatsVehicule : $voyageStatsRemorque)->get($id, ['count' => 0, 'sum' => 0]);
            $maintenance = ($type === 'vehicule' ? $maintenanceStatsVehicule : $maintenanceStatsRemorque)->get($id, 0);
            $charges = ($type === 'vehicule' ? $chargesStatsVehicule : $chargesStatsRemorque)->get($id, 0);
            $coutSocial = ($type === 'vehicule' ? $socialCostsVehicule->get($id, 0) : 0);

            $revenus = (float)$voyage['sum'];
            $depensesTotales = (float)($maintenance + $charges + $coutSocial);
            $margeNette = $revenus - $depensesTotales;
            
            $valeurInitial = (float)($asset->valeur_initial ?? 0);
            $roi = ($valeurInitial > 0) ? ($margeNette / $valeurInitial) * 100 : 0;

            return [
                'id' => $id,
                'type' => $type,
                'label' => $item['label'],
                'sub_label' => ($type === 'vehicule') ? "{$asset->marque} {$asset->modele}" : $asset->marque_remorque,
                'img' => $asset->img ?? null,
                'valeur_initial' => $valeurInitial,
                'nb_voyages' => $voyage['count'],
                'revenus' => $revenus,
                'depense_maintenance' => (float)$maintenance,
                'charges_route' => (float)$charges,
                'cout_social' => (float)$coutSocial,
                'depenses_totales' => $depensesTotales,
                'marge_nette' => $margeNette,
                'roi' => (float)round($roi, 2),
            ];
        });
    }

    private function getAggregatedVoyages($ids, $field, $start, $end)
    {
        return Voyage::whereIn($field, $ids)
            ->when($start, fn($q) => $q->where('date_voyage', '>=', $start))
            ->when($end, fn($q) => $q->where('date_voyage', '<=', $end))
            ->select($field, DB::raw('count(*) as count'), DB::raw('sum(montant) as sum'))
            ->groupBy($field)
            ->get()
            ->keyBy($field);
    }

    private function getAggregatedMaintenance($ids, $field, $start, $end)
    {
        return ReparationVehicule::whereIn($field, $ids)
            ->when($start, fn($q) => $q->where('date_reparation', '>=', $start))
            ->when($end, fn($q) => $q->where('date_reparation', '<=', $end))
            ->select($field, DB::raw('sum(montant_total) as sum'))
            ->groupBy($field)
            ->get()
            ->pluck('sum', $field);
    }

    private function getAggregatedCharges($ids, $field, $start, $end)
    {
        return VoyageCharge::whereHas('voyage', function ($q) use ($ids, $field, $start, $end) {
            $q->whereIn($field, $ids)
                ->when($start, fn($q) => $q->where('date_voyage', '>=', $start))
                ->when($end, fn($q) => $q->where('date_voyage', '<=', $end));
        })
        ->select(DB::raw("voyages.$field as asset_id"), DB::raw('sum(voyage_charges.montant) as sum'))
        ->join('voyages', 'voyage_charges.voyage_id', '=', 'voyages.id')
        ->groupBy("voyages.$field")
        ->get()
        ->pluck('sum', 'asset_id');
    }

    private function getBulkSocialCosts($vehiculeIds, $start, $end)
    {
        // This remains the most complex because of the pro-rated logic
        // Let's at least bulk fetch the voyages with chauffeur and aide-chauffeur
        $voyages = Voyage::whereIn('vehicule_id', $vehiculeIds)
            ->when($start, fn($q) => $q->where('date_voyage', '>=', $start))
            ->when($end, fn($q) => $q->where('date_voyage', '<=', $end))
            ->select('id', 'vehicule_id', 'chauffeur_id', 'aide_chauffeur_id', 'date_voyage')
            ->get()
            ->groupBy('vehicule_id');

        $costs = collect();
        foreach ($voyages as $vehiculeId => $vehiculeVoyages) {
            $total = 0;
            foreach ($vehiculeVoyages as $voyage) {
                $date = Carbon::parse($voyage->date_voyage);
                if ($voyage->chauffeur_id) $total += $this->getProRatedSalary($voyage->chauffeur_id, $date->month, $date->year);
                if ($voyage->aide_chauffeur_id) $total += $this->getProRatedSalary($voyage->aide_chauffeur_id, $date->month, $date->year);
            }
            $costs->put($vehiculeId, $total);
        }
        return $costs;
    }

    private function getProRatedSalary($chauffeurId, $mois, $annee): float
    {
        // Simple static cache to avoid redundant queries in the same request
        static $cache = [];
        $key = "{$chauffeurId}-{$mois}-{$annee}";
        if (isset($cache[$key])) return $cache[$key];

        $salarie = Salarie::whereHas('chauffeur', function($q) use ($chauffeurId) {
            $q->where('id', $chauffeurId);
        })->first();

        if (!$salarie) return $cache[$key] = 0;

        $paie = Paie::where('salarie_id', $salarie->id)
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->first();

        $salaireMensuel = $paie ? $paie->salaire_net : ($salarie->salaire ?? 0);
        return $cache[$key] = (float)($salaireMensuel / 22); 
    }

    /**
     * Get all profitability data for export.
     */
    public function getAllProfitability(array $filters = []): Collection
    {
        $search = $filters['search'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        $vehicules = Vehicule::query()
            ->when($search, function ($q) use ($search) {
                $q->where('immatriculation', 'LIKE', "%$search%")
                    ->orWhere('marque', 'LIKE', "%$search%")
                    ->orWhere('modele', 'LIKE', "%$search%");
            })
            ->get()
            ->map(fn($v) => ['id' => $v->id, 'type' => 'vehicule', 'model' => $v, 'label' => $v->immatriculation]);

        $remorques = Remorque::query()
            ->when($search, function ($q) use ($search) {
                $q->where('numero_remorque', 'LIKE', "%$search%")
                    ->orWhere('marque_remorque', 'LIKE', "%$search%");
            })
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'type' => 'remorque', 'model' => $r, 'label' => $r->numero_remorque]);

        $allAssets = $vehicules->concat($remorques);

        $results = $this->calculateBulkStats($allAssets, $startDate, $endDate);

        return $results->sortByDesc('marge_nette')->values();
    }

    /**
     * Get global summary stats (for KPI cards)
     */
    public function getGlobalSummary(array $filters = []): array
    {
        // We can reuse the calculation logic but we want it for the WHOLE dataset (all pages)
        // Since getPaginatedProfitability already calculates for all assets before paginating,
        // we can extract the summary from the full collection.
        $search = $filters['search'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        // Fetch all matching assets
        $vehicules = Vehicule::query()
            ->when($search, function ($q) use ($search) {
                $q->where('immatriculation', 'LIKE', "%$search%")
                    ->orWhere('marque', 'LIKE', "%$search%")
                    ->orWhere('modele', 'LIKE', "%$search%");
            })
            ->get()
            ->map(fn($v) => ['id' => $v->id, 'type' => 'vehicule', 'model' => $v, 'label' => $v->immatriculation]);

        $remorques = Remorque::query()
            ->when($search, function ($q) use ($search) {
                $q->where('numero_remorque', 'LIKE', "%$search%")
                    ->orWhere('marque_remorque', 'LIKE', "%$search%");
            })
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'type' => 'remorque', 'model' => $r, 'label' => $r->numero_remorque]);

        $allAssets = $vehicules->concat($remorques);
        $results = $this->calculateBulkStats($allAssets, $startDate, $endDate);

        return [
            'total_revenus' => (float)$results->sum('revenus'),
            'total_marge' => (float)$results->sum('marge_nette'),
            'total_charges' => (float)$results->sum('depenses_totales'),
            'total_investissement' => (float)$results->sum('valeur_initial'),
            'average_roi' => $results->sum('valeur_initial') > 0 ? ($results->sum('marge_nette') / $results->sum('valeur_initial')) * 100 : 0
        ];
    }
}
