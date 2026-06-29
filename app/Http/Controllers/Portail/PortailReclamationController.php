<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortailReclamationIndexRequest;
use App\Http\Requests\StoreReclamationRequest;
use App\Models\Client;
use App\Models\Reclamation;
use App\Services\PortailReclamationService;
use App\Utils\Portail\ExtractFiltrePortailReclamation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortailReclamationController extends Controller
{
    public function __construct(private PortailReclamationService $reclamationService) {}

    private function sessionKey(): string
    {
        return 'portail_client_id_' . tenant('id');
    }

    private function getClient(): Client
    {
        return Client::findOrFail(session($this->sessionKey()));
    }

    public function index(PortailReclamationIndexRequest $request)
    {
        $client  = $this->getClient();
        $filters = ExtractFiltrePortailReclamation::extractFilter($request);
        $filters['client_id'] = $client->id;

        $data    = $this->reclamationService->getAll($filters);
        $voyages = $this->reclamationService->getClientVoyages($client->id);

        return Inertia::render('Portail/Reclamations/Index', [
            'client'  => $client,
            'data'    => $data,
            'voyages' => $voyages,
            'filters' => $filters,
        ]);
    }

    public function store(StoreReclamationRequest $request)
    {
        $client = $this->getClient();

        $this->reclamationService->store(
            $client->id,
            $request->validated(),
            $request->file('images', []),
        );

        return back()->with('success', 'Votre réclamation a été soumise avec succès.');
    }

    public function show(Reclamation $reclamation)
    {
        $client = $this->getClient();

        abort_if($reclamation->client_id !== $client->id, 403);

        $reclamation->load(['images', 'voyage']);

        return Inertia::render('Portail/Reclamations/Show', [
            'client'      => $client,
            'reclamation' => $reclamation,
        ]);
    }
}
