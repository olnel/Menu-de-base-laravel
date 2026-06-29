<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortailEvaluationIndexRequest;
use App\Http\Requests\PortailEvaluationStoreRequest;
use App\Models\Client;
use App\Models\Evaluation;
use App\Services\PortailEvaluationService;
use App\Utils\Portail\ExtractFiltrePortailEvaluation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortailEvaluationController extends Controller
{
    public function __construct(private PortailEvaluationService $evaluationService) {}

    private function sessionKey(): string
    {
        return 'portail_client_id_' . tenant('id');
    }

    private function getClient(): Client
    {
        return Client::findOrFail(session($this->sessionKey()));
    }

    public function index(PortailEvaluationIndexRequest $request)
    {
        $client  = $this->getClient();
        $filters = ExtractFiltrePortailEvaluation::extractFilter($request);
        $filters['client_id'] = $client->id;

        $data    = $this->evaluationService->getAll($filters);
        $voyages = $this->evaluationService->getClientVoyagesNonEvalues($client->id);
        $stats   = $this->evaluationService->getStats($client->id);

        return Inertia::render('Portail/Evaluation/Index', [
            'client'  => $client,
            'data'    => $data,
            'voyages' => $voyages,
            'stats'   => $stats,
            'filters' => $filters,
        ]);
    }

    public function store(PortailEvaluationStoreRequest $request)
    {
        $client = $this->getClient();

        if ($this->evaluationService->hasEvaluatedVoyage($client->id, $request->validated()['voyage_id'])) {
            return back()->withErrors(['voyage_id' => 'Vous avez déjà évalué ce voyage.']);
        }

        $this->evaluationService->store($client->id, $request->validated());

        return back()->with('success', 'Votre évaluation a été soumise avec succès. Merci !');
    }

    public function update(PortailEvaluationStoreRequest $request, Evaluation $evaluation)
    {
        $client = $this->getClient();

        if ($evaluation->client_id !== $client->id) {
            abort(403);
        }

        $this->evaluationService->update($evaluation, $request->validated());

        return back()->with('success', 'Votre évaluation a été mise à jour.');
    }

    public function destroy(Evaluation $evaluation)
    {
        $client = $this->getClient();

        if ($evaluation->client_id !== $client->id) {
            abort(403);
        }

        $this->evaluationService->delete($evaluation);

        return back()->with('success', 'Votre évaluation a été supprimée.');
    }
}
