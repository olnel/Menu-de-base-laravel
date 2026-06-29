<?php

namespace App\Http\Controllers;

use App\Models\CarburantCard;
use App\Models\Chauffeur;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\Voyage;
use App\Models\Vehicule;
use App\Utils\ExtractFiltreVoyage;
use Illuminate\Http\Request;
use App\Services\VoyageService;
use App\Services\RemorqueService;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\Immobilisation;
use App\Models\Tarif;
use App\Models\Tresorerie;
use App\Services\CarburantCardService;
use App\Services\ChauffeurService;
use App\Services\ClientService;
use App\Services\TarifService;
use App\Services\TresorerieService;
use App\Services\VehiculeService;
use App\Services\VoyagePneuService;

class VoyageController extends Controller
{

    protected VoyageService $voyageService;
    protected Voyage $voyage;
    protected RemorqueService $remorqueService;
    protected TresorerieService $tresorerieService;
    protected CarburantCardService $carburantCardService;
    protected ClientService $clientService;
    protected ChauffeurService $chauffeurService;
    protected VehiculeService $vehiculeService;
    protected TarifService $tarifService;
    protected VoyagePneuService $voyagePneuService;

    public function __construct(VoyageService $voyageService, Voyage $voyage, VehiculeService $vehiculeService, RemorqueService $remorqueService, TresorerieService $tresorerieService, CarburantCardService $carburantCardService, ClientService $clientService, ChauffeurService $chauffeurService, TarifService $tarifService, VoyagePneuService $voyagePneuService)
    {
        $this->voyageService = $voyageService;
        $this->voyage = $voyage;
        $this->carburantCardService = $carburantCardService;
        $this->remorqueService = $remorqueService;
        $this->tresorerieService = $tresorerieService;
        $this->clientService = $clientService;
        $this->chauffeurService = $chauffeurService;
        $this->vehiculeService = $vehiculeService;
        $this->tarifService = $tarifService;
        $this->voyagePneuService = $voyagePneuService;
    }

    public function details(Voyage $voyage)
    {
        $voyage->load('vehicule.chauffeurs', 'vehicule.remorque', 'voyageMarchandises', 'voyageCharges', 'carburantTransactions', 'reservation');
        $immobilisation_value = Immobilisation::first()?->valeur;
        // Charger tous les véhicules avec leurs relations pour le select
        $vehicules = $this->vehiculeService->getVehiculesWithChauffeursAndRemorque();
        // Charger toutes les remorques disponibles avec le service
        $remorques = $this->remorqueService->getAll([]);
        // Charger tous les chauffeurs disponibles
        $chauffeurs = $this->chauffeurService->getMappedChauffeurs();
        // Charger toutes les cartes carburant
        $carburantCards = $this->carburantCardService->getMappedCarburantCards();
        // Charger toutes les options de lieux depuis la table voyages (depart et destination)
        $all_lieu_options = $this->voyageService->getAllLieuOptions();
        // Charger tous les tarifs avec leurs détails
        $tariffs_with_details = $this->tarifService->getTarifsWithDetailsMapped();
        // Charger toutes les trésoreries disponibles
        $tresoreries = $this->tresorerieService->getAll([]);

        return back()->with([
            'flash' => [
                'voyage' => $voyage,
                'immobilisation_value' => $immobilisation_value,
                'vehicules' => $vehicules,
                'remorques' => $remorques,
                'chauffeurs' => $chauffeurs, // Ajouter tous les chauffeurs disponibles
                'carburant_cards' => $carburantCards,
                'all_tariffs_with_details' => $tariffs_with_details,
                'lieu_livraison_options' => $all_lieu_options,
                'tresoreries' => $tresoreries,
                'voyage_pneus' => $this->voyagePneuService->getPneusForVoyage($voyage),
            ]
        ]);
    }

    public function index(Request $request)
    {
        $filtre = ExtractFiltreVoyage::extractFilter($request);
        $output = $this->voyageService->getAll($filtre);
        $vehicules = $this->vehiculeService->getAll([]);
        $chauffeurs = $this->chauffeurService->getMappedChauffeurs();
        $clients = $this->clientService->getAll([]);
        
        return Inertia::render('Voyage/Index', [
            "data" => $output,
            "filters" => [],
            "vehicules" => $vehicules,
            "chauffeurs" => $chauffeurs,
            "clients" => $clients,
            "flash" => session('flash', []),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'destination' => 'required|string|max:255',
            'type_trajet' => ['nullable', Rule::in(['local', 'regional', 'inter_region', 'express'])],
            'etat_reception' => 'nullable|in:complet,partiel,rejete',
            'mobilisation' => 'nullable|numeric',
            'commentaire' => 'nullable|string',
            'etat_vehicule_avant' => 'nullable|string',
            'etat_vehicule_apres' => 'nullable|string',
            'montant' => 'required|numeric',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'aide_chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'tarif_ht' => 'required|numeric',
            'kilometrage' => 'required|numeric|min:1',
            'tarif_ht_total' => 'nullable|numeric',
            'montant_ht' => 'nullable|numeric',
            'valeur_tva' => 'nullable|numeric|min:0|max:100',
            'nbr_jour' => 'nullable|integer|min:0',
            'vehicule_id' => 'required|exists:vehicules,id',
            'remorque_id' => 'required|exists:remorques,id',
            'date_voyage' => 'required|date',
            'reservation_id' => 'required|exists:reservations,id',
            'numero_voyage' => 'required|string|unique:voyages,numero_voyage',
            'depart' => 'nullable|string|max:255',
            'description' => 'required|string',
            'remise' => 'nullable|numeric',
            'apply_kilometrage' => 'nullable|boolean',
            'statut' => 'nullable|in:planifie,en_route,arrive,livre,annule',
            'km_parcouru' => 'nullable|numeric|min:0',
            'poids_transporte' => 'nullable|numeric|min:0',
            'heures_facturables' => 'nullable|numeric|min:0',
            'heures_non_facturables' => 'nullable|numeric|min:0',
        ]);
        $output = $this->voyageService->create($validatedData);
        if ($output['error']) {
            return back()->with('message.error', $output['message']);
        }
        $voyage = $output['data'];
        $voyage->load($this->voyageService->getRelation());
        return back()->with([
            'message.success' => $output['message'],
            'flash' => ['data' => $voyage]
        ]);
    }

    public function update(Request $request, Voyage $voyage)
    {
        $rules = [
            'destination' => 'required|string|max:255',
            'type_trajet' => ['nullable', Rule::in(['local', 'regional', 'inter_region', 'express'])],
            'etat_reception' => 'nullable|in:complet,partiel,rejete',
            'mobilisation' => 'nullable|numeric',
            'commentaire' => 'nullable|string',
            'etat_vehicule_avant' => 'nullable|string',
            'etat_vehicule_apres' => 'nullable|string',
            'montant' => 'required|numeric',
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'aide_chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'tarif_ht' => 'nullable|numeric',
            'kilometrage' => 'required|numeric|min:1',
            'tarif_ht_total' => 'nullable|numeric',
            'montant_ht' => 'nullable|numeric',
            'valeur_tva' => 'nullable|numeric|min:0|max:100',
            'nbr_jour' => 'nullable|integer|min:0',
            'remorque_id' => 'required|exists:remorques,id',
            'date_voyage' => 'required|date',
            'depart' => 'nullable|string|max:255',
            'description' => 'required|string',
            'remise' => 'nullable|numeric',
            'apply_kilometrage' => 'nullable|boolean',
            'statut' => 'nullable|in:planifie,en_route,arrive,livre,annule',
            'km_parcouru' => 'nullable|numeric|min:0',
            'poids_transporte' => 'nullable|numeric|min:0',
            'heures_facturables' => 'nullable|numeric|min:0',
            'heures_non_facturables' => 'nullable|numeric|min:0',
        ];
        $validatedData = $request->validate($rules);
        $output = $this->voyageService->update($voyage, $validatedData);
        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function updateSuivi(Request $request, Voyage $voyage)
    {
        $validated = $request->validate([
            'statut'                => 'nullable|in:planifie,en_route,arrive,livre,annule',
            'km_parcouru'           => 'nullable|numeric|min:0',
            'poids_transporte'      => 'nullable|numeric|min:0',
            'heures_facturables'    => 'nullable|numeric|min:0',
            'heures_non_facturables'=> 'nullable|numeric|min:0',
        ]);
        $voyage->update($validated);
        return back()->with('message.success', 'Suivi mis à jour avec succès.');
    }

    public function destroy(Voyage $voyage)
    {
        $output = $this->voyageService->delete($voyage);
        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
