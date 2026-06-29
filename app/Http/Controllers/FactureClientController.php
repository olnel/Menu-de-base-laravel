<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFactureClientRequest;
use App\Http\Requests\UpdateFactureClientRequest;
use App\Models\FactureClient;
use App\Services\ClientService;
use App\Services\FactureClientService;
use App\Services\TresorerieService;
use App\Utils\ExtractFiltreFactureClient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FactureClientController extends Controller
{

    public function __construct(private readonly FactureClientService $service, private readonly TresorerieService $tresorerieService, private readonly ClientService $clientService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreFactureClient::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $tresoreries = $this->tresorerieService->getAll([]);
        $client = $this->clientService->getAll([]);

        return Inertia::render("FactureClient/Index", [
            "data" => $output,
            "tresoreries" => $tresoreries,
            "clients" => $client,
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
    public function store(StoreFactureClientRequest $request)
    {
        $output = $this->service->saveFacture($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(FactureClient $factureclient)
    {

        return back()->with([
            'flash' => [
                'data' => $factureclient
            ]
        ]);
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
    public function update(UpdateFactureClientRequest $request, FactureClient $factureclient)
    {
        $output = $this->service->updateFacture($factureclient,$request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function generateInvoiceNumber()
    {
        $output = $this->service->generateInvoiceNumber();

        return back()->with([
            'flash' => [
                'data' => $output,
            ]
        ]);
    }

    public function print(FactureClient $factureclient)
    {
        try {
            return $this->service->generatePdf($factureclient);
        } catch (\Exception $e) {
            abort(500, "Échec de génération du PDF : " . $e->getMessage());
        }

        /*try {
            // Appelle la nouvelle méthode du service pour obtenir le HTML
            $html = $this->service->getHtmlForPrint($factureclient);

            // Retourne le HTML comme une réponse simple
            return response($html);
        } catch (\Exception $e) {
            abort(500, "Échec de la préparation de la vue : " . $e->getMessage());
        }*/

    }

    public function sendMail(FactureClient $factureclient)
    {
        try {
            $this->service->sendMail($factureclient);
            return back()->with('message.success', 'Email envoyé avec succès');
        } catch (\Exception $e) {
            abort(500, "Échec de génération du PDF : " . $e->getMessage());
        }
    }


    public function fetchReglement(FactureClient $factureclient)
    {
        $factureclient->load(['reglements']);
        return back()->with([
            'flash' => [
                'data' => $factureclient
            ]
        ]);
    }

}
