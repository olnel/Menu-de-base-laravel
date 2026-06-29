<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\DocumentChauffeur;
use App\Models\Vehicule;
use App\Services\ChauffeurService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Validation\Rule;



class ChauffeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $service;

    public function __construct(ChauffeurService $chauffeurService)
    {
        $this->service = $chauffeurService;
    }
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $vehicules = Vehicule::get();

        $documents = null;
        $chauffeurWithDocuments = null;
        if ($request->has('chauffeur_id')) {
            $documents = DocumentChauffeur::where('chauffeur_id', $request->chauffeur_id)->get();
        }
        $inertiaData = [
            "data" => $output,
            "vehicules" => $vehicules,
            "filters" => [
                "search" => $request->search
            ],
            "documents" => $documents,
        ];
        return Inertia::render("Chauffeur/Index", $inertiaData);
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
    public function store(Request $request)
    {

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'matricule' => [
                'required',
                'string',
                'max:255',
                Rule::unique('chauffeurs')->whereNull('deleted_at'),
            ],
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'CIN' => 'nullable|string|max:20|unique:chauffeurs,CIN',
            'img' => 'nullable|file|mimes:jpeg,png,jpg',
        ]);
        // Conversion Carbon (format d/m/Y venant du front)
        if ($request->has('date_naissance')) {
            $validated['date_naissance'] = Carbon::parse($request->date_naissance)->format('Y-m-d');
        }

        $this->service->create($validated);

        // Synchronisation des véhicules après création
        // On récupère l'instance du chauffeur via le champ unique CIN
        $chauffeur = Chauffeur::where('matricule', $validated['matricule'])->first();
        if ($chauffeur && $request->has('vehicules')) {
            $chauffeur->vehicules()->sync($request->vehicules);
        }

        return redirect()->back()->with('message.success', 'Chauffeur ajouté avec succès');
    }





    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch the chauffeur and eager load their documents
        $chauffeur = Chauffeur::with('documents', 'vehicules')->findOrFail($id); // Assuming 'documents' is the relationship name
        return Inertia::render('Chauffeur/DetailChauffeur', ['chauffeur' => $chauffeur]);
    }

    public function showInformations(Chauffeur $chauffeur)
    {
        // Récupère le chauffeur avec ses documents et véhicules associés
        $chauffeur = Chauffeur::with('documents', 'vehicules')->findOrFail($chauffeur->id);

        // Récupère tous les véhicules disponibles pour le formulaire de modification du chauffeur
        $vehicules = Vehicule::all();

        return Inertia::render('Chauffeur/Information', [
            'chauffeur' => $chauffeur,
            'vehicules' => $vehicules, // Passe tous les véhicules au formulaire de chauffeur
        ]);
    }






    public function edit(string $id)
    {
        //
    }







    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $chauffeur = Chauffeur::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'matricule' => [
                'required',
                'string',
                'max:255',
                Rule::unique('chauffeurs')->whereNull('deleted_at')->ignore($chauffeur->id),
            ],
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'CIN' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('chauffeurs')->ignore($chauffeur->id),
            ],
            'img' => 'nullable|image|max:2048',
        ]);
        $this->service->update($chauffeur, $validated);

        // Synchronisation des véhicules après mise à jour
        if ($request->has('vehicules')) {
            $chauffeur->vehicules()->sync($request->vehicules);
        }

        return redirect()->back()->with('message.success', 'Chauffeur modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chauffeur $chauffeur)
    {
        $output = $this->service->delete($chauffeur);

        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }

        return back()->with('message.success', $output['message']);
    }

}
