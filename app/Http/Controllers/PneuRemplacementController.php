<?php

namespace App\Http\Controllers;

use App\Models\PneuRemplacement;
use App\Services\MagasinService;
use App\Services\PneuRemplacementService;
use App\Services\PneuSerieService;
use App\Services\RemorqueService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltrePneuRemplacement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PneuRemplacementController extends Controller
{
    public function __construct(
        private PneuRemplacementService $service,
        private VehiculeService         $vehiculeService,
        private RemorqueService         $remorqueService,
        private MagasinService          $magasinService,
        private PneuSerieService        $pneuSerieService,
    ) {}

    public function index(Request $request)
    {
        $filtre    = ExtractFiltrePneuRemplacement::extractFilter($request);
        $output    = $this->service->getAll($filtre);
        $vehicules = $this->vehiculeService->getAll([]);
        $remorques = $this->remorqueService->getAll([]);
        $magasins  = $this->magasinService->getAll([]);

        return Inertia::render('PneuRemplacement/Index', [
            'data'      => $output,
            'vehicules' => $vehicules,
            'remorques' => $remorques,
            'magasins'  => $magasins,
        ]);
    }

    public function store(Request $request)
    {
        $base = [
            'type_operation' => 'required|string|in:remplacement,permutation',
            'date_operation' => 'required|date',
            'vehicule_id'    => 'nullable|numeric|exists:vehicules,id',
            'remorque_id'    => 'nullable|numeric|exists:remorques,id',
            'technicien'     => 'nullable|string|max:255',
            'observations'   => 'nullable|string|max:1000',
        ];

        if ($request->input('type_operation') === 'remplacement') {
            $rules = array_merge($base, [
                'lignes'                        => 'required|array|min:1',
                'lignes.*.pneu_serie_retire_id' => 'required|numeric|exists:pneu_series,id',
                'lignes.*.pneu_serie_monte_id'  => 'nullable|numeric|exists:pneu_series,id',
                'lignes.*.position'             => 'nullable|string|max:100',
                'lignes.*.motif'                => 'nullable|string|in:usure,crevaison,vol,fin_vie,autre',
                'lignes.*.date_hors_service'    => 'nullable|date',
            ]);
        } else {
            $rules = array_merge($base, [
                'lignes'                         => 'required|array|min:1',
                'lignes.*.pneu_serie_retire_id'  => 'required|numeric|exists:pneu_series,id',
                'lignes.*.pneu_serie_monte_id'   => 'nullable|numeric|exists:pneu_series,id',
                'lignes.*.position_retire'        => 'nullable|string|max:100',
                'lignes.*.position_monte'         => 'nullable|string|max:100',
                'lignes.*.motif'                  => 'nullable|string|in:usure,crevaison,vol,fin_vie,autre',
                'lignes.*.date_hors_service'      => 'nullable|date',
            ]);
        }

        $validated = $request->validate($rules);

        $output = $this->service->save($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function update(Request $request, PneuRemplacement $pneu_remplacement)
    {
        $validated = $request->validate([
            'type_operation'       => 'required|string|in:remplacement,permutation',
            'date_operation'       => 'required|date',
            'vehicule_id'          => 'nullable|numeric|exists:vehicules,id',
            'remorque_id'          => 'nullable|numeric|exists:remorques,id',
            'pneu_serie_retire_id' => 'required|numeric|exists:pneu_series,id',
            'pneu_serie_monte_id'  => 'nullable|numeric|exists:pneu_series,id',
            'position_retire'      => 'nullable|string|max:100',
            'position_monte'       => 'nullable|string|max:100',
            'motif'                => 'nullable|string|in:usure,crevaison,vol,fin_vie,autre',
            'date_hors_service'    => 'nullable|date',
            'technicien'           => 'nullable|string|max:255',
            'observations'         => 'nullable|string|max:1000',
        ]);

        $output = $this->service->update($pneu_remplacement, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function destroy(PneuRemplacement $pneu_remplacement)
    {
        $output = $this->service->delete($pneu_remplacement);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Recherche de pneus avec autocomplete (max 10 résultats).
     * Contexte : vehicule_id OU remorque_id OU magasin_id (stock libre).
     */
    public function searchPneus(Request $request)
    {
        $validated = $request->validate([
            'search'      => 'nullable|string|max:100',
            'vehicule_id' => 'nullable|numeric|exists:vehicules,id',
            'remorque_id' => 'nullable|numeric|exists:remorques,id',
            'magasin_id'  => 'nullable|numeric|exists:magasins,id',
        ]);

        $pneus = $this->pneuSerieService->searchPneus(
            $validated['search']      ?? null,
            isset($validated['vehicule_id']) ? (int) $validated['vehicule_id'] : null,
            isset($validated['remorque_id']) ? (int) $validated['remorque_id'] : null,
            isset($validated['magasin_id'])  ? (int) $validated['magasin_id']  : null,
        );

        return response()->json(['pneus' => $pneus]);
    }
}
