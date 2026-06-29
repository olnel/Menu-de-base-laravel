<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortailHistoriqueIndexRequest;
use App\Models\Client;
use App\Services\PortailHistoriqueService;
use App\Utils\Portail\ExtractFiltrePortailHistorique;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortailHistoriqueController extends Controller
{
    public function __construct(private PortailHistoriqueService $historiqueService) {}

    private function sessionKey(): string
    {
        return 'portail_client_id_' . tenant('id');
    }

    private function getClient(): Client
    {
        return Client::findOrFail(session($this->sessionKey()));
    }

    public function index(PortailHistoriqueIndexRequest $request)
    {
        $client  = $this->getClient();
        $filters = ExtractFiltrePortailHistorique::extractFilter($request);
        $filters['client_id'] = $client->id;

        $data = $this->historiqueService->getAll($filters);

        return Inertia::render('Portail/Historique/Index', [
            'client'  => $client,
            'data'    => $data,
            'filters' => $filters,
        ]);
    }
}
