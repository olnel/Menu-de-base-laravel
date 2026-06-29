<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Salarie;
use App\Models\SessionFormation;
use App\Services\FormationService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormationController extends Controller
{
    private FormationService $service;

    public function __construct(FormationService $service)
    {
        $this->service = $service;
    }

    /**
     * Liste des formations
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);

        $formations = $this->service->getAll($filtre);
        $salaries = Salarie::all(['id', 'nom', 'prenom', 'matricule']);

        return Inertia::render('Formation/Index', [
            'data' => $formations,
            'salaries' => $salaries,
            'filters' => [
                'search' => $request->search,
            ],
            'flash' => session('flash', []),
        ]);
    }

    /**
     * Affiche les détails d'une formation (sessions, participants)
     */
    public function show(Formation $formation)
    {
        $formation->load([
            'sessionFormations' => function ($q) {
                $q->latest('date_formation')->with('participants.salarie');
            },
            'formationSuivante',
        ]);

        return back()->with([
            'flash' => [
                'data' => $formation,
            ],
        ]);
    }

    /**
     * Crée une formation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'periode_renouvellement_mois' => 'required|integer|min:1',
            'alerte_avant_jours' => 'required|integer|min:0',
            'formation_suivante_id' => 'nullable|exists:formations,id',
        ]);

        $output = $this->service->createFormation($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Met à jour une formation
     */
    public function update(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'periode_renouvellement_mois' => 'required|integer|min:1',
            'alerte_avant_jours' => 'required|integer|min:0',
            'formation_suivante_id' => 'nullable|exists:formations,id',
        ]);

        $output = $this->service->updateFormation($formation, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Supprime une formation
     */
    public function destroy(Formation $formation)
    {
        try {
            $output = $this->service->deleteFormation($formation);
            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );
        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }

    /**
     * Crée une session de formation avec participants
     */
    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'date_formation' => 'required|date',
            'observation' => 'nullable|string',
            'salarie_ids' => 'present|array',
            'salarie_ids.*' => 'exists:salaries,id',
        ]);

        $output = $this->service->createSession($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Récupère les participants de la dernière session d'une formation
     * (utilisé quand on clique sur une notification)
     */
    public function derniereSessionParticipants(Formation $formation)
    {
        $data = $this->service->getParticipantsDerniereSession($formation->id);

        return response()->json($data);
    }

    /**
     * Récupère les participants d'une session spécifique
     */
    public function sessionParticipants(SessionFormation $session)
    {
        $session->load(['formation', 'participants.salarie']);

        return response()->json([
            'session' => $session,
            'participants' => $session->participants->map(fn ($p) => $p->salarie),
        ]);
    }

    /**
     * Récupère les participants de la formation PRÉCÉDENTE (A)
     * pour pré-sélectionner quand on crée une session pour la formation suivante (B)
     */
    public function participantsPrecedente(Formation $formation)
    {
        // Trouver la formation qui précède (celle qui a formation_suivante_id = formation.id)
        $precedente = Formation::where('formation_suivante_id', $formation->id)->first();
        if (!$precedente) {
            return response()->json(['participants' => []]);
        }
        $data = $this->service->getParticipantsDerniereSession($precedente->id);
        return response()->json($data);
    }

    /**
     * Récupère la liste des formations (pour select)
     */
    public function liste()
    {
        $formations = Formation::all(['id', 'nom']);
        return response()->json($formations);
    }

    /**
     * Récupère toutes les sessions d'une formation
     */
    public function getSessions(Formation $formation)
    {
        $sessions = $formation->sessionFormations()
            ->latest('date_formation')
            ->get(['id', 'date_formation', 'observation']);

        return response()->json($sessions);
    }

    /**
     * Imprime la liste des participants pour une session donnée sous forme de PDF streamé
     */
    public function printSession(SessionFormation $session, \App\Services\PDFService\PDFService $pdfService)
    {
        $session->load(['formation', 'participants.salarie']);
        $participants = $session->participants->map(fn ($p) => $p->salarie);

        $societe = \App\Models\InfoSociete::first();
        if ($societe && !isset($societe->nom)) {
            $societe->nom = $societe->nom_societe;
        }

        $title = "Liste_Participants_" . str_replace(' ', '_', $session->formation->nom) . "_" . ($session->date_formation ? $session->date_formation->format('Ymd') : date('Ymd'));

        return $pdfService
            ->setFilename($title . ".pdf")
            ->setOrientation("portrait")
            ->setPaperSize("A4")
            ->stream("pdf.formation-session", compact('session', 'participants', 'societe'));
    }
}
