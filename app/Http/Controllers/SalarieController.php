<?php

namespace App\Http\Controllers;

use App\Models\Salarie;
use App\Models\Vehicule;
use App\Models\Chauffeur;
use App\Services\SalarieService;
use App\Services\TypeSalarieService;
use App\Services\DocumentDynamicService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Carbon\Carbon;

class SalarieController extends Controller
{
    private $service;
    private $typeSalarieService;
    private $documentService;

    public function __construct(
        SalarieService $salarieService, 
        TypeSalarieService $typeSalarieService,
        DocumentDynamicService $documentService
    ) {
        $this->service = $salarieService;
        $this->typeSalarieService = $typeSalarieService;
        $this->documentService = $documentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        
        if ($request->has('deleted')) {
            $filtre['only_trashed'] = true;
        }

        $output = $this->service->getAll($filtre);
        $types = $this->typeSalarieService->getAll([]);
        $requiredDocs = $this->documentService->getRequiredDocumentModels(Salarie::class);
        $vehicules = Vehicule::all(['id', 'immatriculation', 'marque', 'modele']);
        $chauffeurs = Chauffeur::where('is_aide_chauffeur', false)->get(['id', 'nom', 'prenom', 'matricule']);

        return Inertia::render("Salarie/Index", [
            "data" => $output,
            "types_salarie" => $types,
            "required_documents" => $requiredDocs,
            "vehicules" => $vehicules,
            "chauffeurs_list" => $chauffeurs,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', [])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'sexe' => ['nullable', Rule::in(['M', 'F'])],
            'salaire' => 'nullable|numeric|min:0',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'nationalite' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'cin' => 'nullable|string|max:255',
            'date_cin' => 'nullable|date',
            'lieu_cin' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'date_embauche' => 'nullable|date',
            'statut' => ['nullable', Rule::in(['actif', 'inactif', 'suspendu'])],
            'observation' => 'nullable|string',
            'type_salarie_id' => 'required|exists:type_salaries,id',
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'parent_chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'documents' => 'nullable|array',
            'documents.*.document_type_id' => 'required|exists:document_types,id',
            'documents.*.fichier' => 'nullable|file|max:10240',
            'documents.*.date_expiration' => 'nullable|date',
            'documents.*.observation' => 'nullable|string',
        ]);

        $output = $this->service->create($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Salarie $salarie)
    {
        $salarie->load('chauffeur');
        return back()->with([
            'flash' => [
                'data' => $salarie
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salarie $salarie)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'sexe' => ['nullable', Rule::in(['M', 'F'])],
            'salaire' => 'nullable|numeric|min:0',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'nationalite' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:255',
            'adresse' => 'nullable|string',
            'cin' => 'nullable|string|max:255',
            'date_cin' => 'nullable|date',
            'lieu_cin' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'date_embauche' => 'nullable|date',
            'statut' => ['nullable', Rule::in(['actif', 'inactif', 'suspendu'])],
            'observation' => 'nullable|string',
            'type_salarie_id' => 'required|exists:type_salaries,id',
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'parent_chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'documents' => 'nullable|array',
            'documents.*.document_type_id' => 'required|exists:document_types,id',
            'documents.*.fichier' => 'nullable|file|max:10240',
            'documents.*.date_expiration' => 'nullable|date',
            'documents.*.observation' => 'nullable|string',
        ]);

        $output = $this->service->update($salarie, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salarie $salarie)
    {
        try {
            $output = $this->service->delete($salarie);
            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );
        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }

    public function history(int $id)
    {
        $salarie = Salarie::withTrashed()->findOrFail($id);
        $history = $salarie->histories()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }
}
