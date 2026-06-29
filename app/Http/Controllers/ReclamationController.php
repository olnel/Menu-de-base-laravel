<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReclamationRequest;
use App\Models\Client;
use App\Models\Reclamation;
use App\Services\ReclamationService;
use App\Utils\ExtractFiltreReclamation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReclamationController extends Controller
{
    public function __construct(private ReclamationService $service) {}

    public function index(Request $request)
    {
        $filters = ExtractFiltreReclamation::extractFilter($request);
        $data    = $this->service->getAll($filters);

        $clients = Client::orderBy('nom_client')
            ->get(['id', 'nom_client'])
            ->map(fn($c) => ['value' => $c->id, 'label' => $c->nom_client]);

        return Inertia::render('Reclamation/Index', [
            'data'    => $data,
            'clients' => $clients,
            'filters' => [
                'search'     => $request->input('search', ''),
                'statut'     => $request->input('statut', ''),
                'client_id'  => $request->input('client_id', ''),
                'start_date' => $request->input('start_date', ''),
                'end_date'   => $request->input('end_date', ''),
            ],
        ]);
    }

    public function update(UpdateReclamationRequest $request, Reclamation $reclamation)
    {
        $output = $this->service->update($reclamation, $request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function destroy(Reclamation $reclamation)
    {
        $output = $this->service->delete($reclamation);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
