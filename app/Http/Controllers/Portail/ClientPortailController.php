<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortailLoginRequest;
use App\Http\Requests\UpdatePortailCompteRequest;
use App\Models\Client;
use App\Services\PortailDashboardService;
use App\Services\VoyageService;
use App\Utils\Portail\ExtractFiltrePortailDashboard;
use App\Utils\Portail\ExtractFiltrePortailVoyage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ClientPortailController extends Controller
{
    private function sessionKey(): string
    {
        return 'portail_client_id_' . tenant('id');
    }

    private function getClient(): Client
    {
        return Client::findOrFail(session($this->sessionKey()));
    }

    public function showLogin()
    {
        if (session()->has($this->sessionKey())) {
            return redirect()->route('portail.menu');
        }

        return Inertia::render('Portail/Login/Login');
    }

    public function login(PortailLoginRequest $request)
    {
        $client = Client::where('login', $request->login)
            ->whereNull('deleted_at')
            ->first();

        if (!$client || !Hash::check($request->mot_de_passe, $client->mot_de_passe)) {
            return back()->withErrors(['login' => 'Identifiant ou mot de passe incorrect.']);
        }

        session([$this->sessionKey() => $client->id]);

        return redirect()->route('portail.menu');
    }

    public function logout()
    {
        session()->forget($this->sessionKey());
        return redirect()->route('portail.login');
    }

    public function menu()
    {
        return Inertia::render('Portail/Menu/Index', [
            'client' => $this->getClient(),
        ]);
    }

    public function dashboard(Request $request, PortailDashboardService $dashboardService)
    {
        $client  = $this->getClient();
        $filters = ExtractFiltrePortailDashboard::extractFilter($request);
        $filters['client_id'] = $client->id;

        $stats                  = $dashboardService->getStats($filters);
        $recentes               = $dashboardService->getRecentes($client->id);
        $factureStats           = $dashboardService->getFactureStats($filters);
        $recentesFactures       = $dashboardService->getRecentesFactures($client->id);
        $reclamationStats       = $dashboardService->getReclamationStats($filters);
        $recentesReclamations   = $dashboardService->getRecentesReclamations($client->id);
        $evaluationStats        = $dashboardService->getEvaluationStats($filters);
        $recentesEvaluations    = $dashboardService->getRecentesEvaluations($client->id);

        return Inertia::render('Portail/Dashboard', [
            'client'                => $client,
            'stats'                 => $stats,
            'recentes'              => $recentes,
            'factureStats'          => $factureStats,
            'recentesFactures'      => $recentesFactures,
            'reclamationStats'      => $reclamationStats,
            'recentesReclamations'  => $recentesReclamations,
            'evaluationStats'       => $evaluationStats,
            'recentesEvaluations'   => $recentesEvaluations,
            'filters'               => $filters,
        ]);
    }

    public function reservation()
    {
        return Inertia::render('Portail/Reservation/Index', [
            'client' => $this->getClient(),
        ]);
    }

    public function tracking(Request $request, VoyageService $voyageService)
    {
        $client = $this->getClient();

        $filtre = ExtractFiltrePortailVoyage::extractFilter($request);
        $filtre['client_id'] = $client->id;

        $voyages = $voyageService->getAll($filtre);

        return Inertia::render('Portail/Tracking/Index', [
            'client'  => $client,
            'voyages' => $voyages,
            'filters' => $filtre,
        ]);
    }

    public function historique()
    {
        return Inertia::render('Portail/Historique/Index', [
            'client' => $this->getClient(),
        ]);
    }

    public function updateCompte(UpdatePortailCompteRequest $request)
    {
        $client = $this->getClient();

        $data = ['login' => $request->login];

        if ($request->filled('nouveau_mot_de_passe')) {
            $data['mot_de_passe'] = $request->nouveau_mot_de_passe;
        }

        $client->update($data);

        return back()->with('success', 'Compte mis à jour avec succès.');
    }

}
