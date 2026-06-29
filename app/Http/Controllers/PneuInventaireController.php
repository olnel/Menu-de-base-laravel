<?php

namespace App\Http\Controllers;

use App\Models\PneuInventaire;
use App\Services\ArticleService;
use App\Services\MagasinService;
use App\Services\PneuInventaireService;
use App\Services\RemorqueService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltrePneuInventaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PneuInventaireController extends Controller
{
    public function __construct(
        private PneuInventaireService $service,
        private MagasinService        $magasinService,
        private ArticleService        $articleService,
        private VehiculeService       $vehiculeService,
        private RemorqueService       $remorqueService,
    ) {}

    public function index(Request $request)
    {
        $filtre    = ExtractFiltrePneuInventaire::extractFilter($request);
        $output    = $this->service->getAll($filtre);
        $magasins  = $this->magasinService->getAll([]);
        $articles  = $this->articleService->getAll(['type_article' => 'Pneu']);
        $vehicules = $this->vehiculeService->getAll([]);
        $remorques = $this->remorqueService->getAll([]);

        return Inertia::render('PneuInventaire/Index', [
            'data'      => $output,
            'magasins'  => $magasins,
            'articles'  => $articles,
            'vehicules' => $vehicules,
            'remorques' => $remorques,
            'filters'   => [],
            'flash'     => session('flash', []),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'magasin_id'                => 'required|numeric|exists:magasins,id',
            'date_inventaire'           => 'required|date',
            'remarque'                  => 'nullable|string|max:255',
            'details'                   => 'required|array|min:1',
            'details.*.numero_serie'    => 'required|string|max:255',
            'details.*.etat'            => 'nullable|string|in:neuf,bon,rechappe',
            'details.*.article_id'      => 'nullable|numeric|exists:articles,id',
            'details.*.is_existe'       => 'nullable|boolean',
            'details.*.occupe'          => 'nullable|boolean',
            'details.*.occupation_type' => 'nullable|string|in:vehicule,remorque',
            'details.*.vehicule_id'     => 'nullable|numeric|exists:vehicules,id',
            'details.*.remorque_id'     => 'nullable|numeric|exists:remorques,id',
            'details.*.type'            => 'nullable|string',
        ]);

        $output = $this->service->save($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Un inventaire pneu est immuable après sa création.
     */
    public function update(Request $request, PneuInventaire $pneu_inventaire)
    {
        return back()->with('message.error', 'Un inventaire pneu ne peut pas être modifié.');
    }

    public function getSupDataByMagasin(Request $request)
    {
        $validated      = $request->validate(['magasin_id' => 'required|numeric|exists:magasins,id']);
        $output         = $this->service->getPneusByMagasin((int) $validated['magasin_id']);
        $isError        = (bool) ($output['error'] ?? false);
        $series         = $isError ? [] : ($output['data'] ?? $output['element'] ?? []);
        $stockTheorique = $this->service->getStockTheoriquePneus((int) $validated['magasin_id']);

        return back()->with([
            'flash' => [
                'pneus_series'    => $series,
                'stock_theorique' => $stockTheorique,
            ],
        ]);
    }
}
