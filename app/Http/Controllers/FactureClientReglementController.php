<?php

namespace App\Http\Controllers;

use App\Http\Requests\FactureclientRequestStore;
use App\Models\FactureClient;
use App\Models\FactureClientReglement;
use App\Services\ClientService;
use App\Services\FactureclientReglementService;
use App\Services\TresorerieService;
use App\Utils\ExtractFiltreFactureClient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactureClientReglementController extends Controller
{
    public function __construct(private readonly FactureclientReglementService $service, private readonly TresorerieService $tresorerieService, private readonly ClientService $clientService){}

    public function index(Request $request)
    {
        $filtre = ExtractFiltreFactureClient::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $tresoreries = $this->tresorerieService->getAll([]);
        $clients =  $this->clientService->getAll([]);

        return Inertia::render("FactureClient/Reglement/Index", [
            "data" => $output,
            "tresoreries" => $tresoreries,
            "clients" => $clients,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', [])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FactureclientRequestStore $request)
    {
        $output = $this->service->todoReglement($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FactureClientReglement $factureclientreglement)
    {
        $output = $this->service->deleteReglement($factureclientreglement);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function historiqueReglement(FactureClient $factureclient)
    {
        $data = $this->service->showHistoriqueReglement($factureclient);
        return back()->with([
            'flash' => [
                'data' => $data
            ]
        ]);
    }
}
