<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDevisclient;
use App\Http\Requests\UdateDevisclient;
use App\Models\BoncommandeFournisseur;
use App\Models\DevisClient;
use App\Services\ClientService;
use App\Services\DevisClientService;
use App\Utils\ExtractFiltreArticle;
use App\Utils\ExtractFiltreDevisClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DevisClientController extends Controller
{
    private DevisClientService $service;
    private ClientService $clientService;

    public function __construct(DevisClientService $devisClientService, ClientService $clientService)
    {
        $this->service = $devisClientService;
        $this->clientService = $clientService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreDevisClient::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $clients = $this->clientService->getAll([]);

        return Inertia::render("DevisClient/Index", [
            "data" => $output,
            'list_clients' => $clients,
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
    public function store(StoreDevisclient $request)
    {
        $output= $this->service->saveDevisWithDetails($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(DevisClient $devisclient)
    {
        $devisclient->load(['details', 'client']);
        return back()->with([
            'flash' => [
                'data' => $devisclient
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
    public function update(UdateDevisclient $request, DevisClient $devisclient)
    {
        $output = $this->service->updateDevisWithDetails($devisclient, $request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DevisClient $devisclient)
    {
        try {
            $output = $this->service->deleteDevisWithDetails($devisclient);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }

    public function generatenumero()
    {
        $output = $this->service->generateNumeroCommande();
        return back()->with([
            'flash' => [
                'data' => $output,
            ]
        ]);
    }


    public function print(DevisClient $devisclient)
    {
//        $devisclient->load(['details', 'client']);
        try {
            return $this->service->generatePdf($devisclient);
        } catch (\Exception $e) {
            abort(500, "Échec de génération du PDF : " . $e->getMessage());
        }
//
//        return view('pdf.devis', ['devis' => $devisclient]);
    }

    public function sendMail(DevisClient $devisclient)
    {
        //try {
        $output = $this->service->sendMail($devisclient);
        return back()->with([
            'flash' => [
                'type' => $output['error'] ? 'error' : 'success',
                'message' => $output['message']
            ]
        ]);
        //  return back()->with('message.success', 'Email envoyé avec succès');
        //} catch (\Exception $e) {
        //     return back()->with('message.error', "Échec de l'envoi : " . $e->getMessage());
        //}
    }
}
