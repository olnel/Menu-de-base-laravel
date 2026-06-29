<?php

namespace App\Http\Controllers;
use App\Models\CarburantCard;
use App\Models\Client;
use App\Models\Vehicule;
use App\Services\VehiculeService;
use App\Models\Chauffeur;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Reservation;
use App\Utils\ExtractFiltre;
use Illuminate\Validation\Rule;
use App\Models\TarifDetail;
use App\Services\TarifService;
use App\Models\Voyage;
use App\Models\Immobilisation;
use App\Models\Tarif;
use App\Models\Tresorerie;
use App\Services\CarburantCardService;
use App\Services\ChauffeurService;
use App\Services\TresorerieService;
use App\Services\ClientService;
use App\Services\VoyageService;
use App\Services\RemorqueService;
use App\Utils\ExtractFiltreReservation;

class ReservationController extends Controller
{
    protected $reservationService;
    protected TresorerieService $tresorerieService;
    protected ClientService $clientService;
    protected VoyageService $voyageService;
    protected RemorqueService $remorqueService;
    protected VehiculeService $vehiculeService;
    protected CarburantCardService $carburantCardService;
    protected TarifService $tarifService;
    protected ChauffeurService $chauffeurService;

    public function __construct(
        ReservationService $reservationService,
        TresorerieService $tresorerieService,
        ClientService $clientService,
        VoyageService $voyageService,
        RemorqueService $remorqueService,
        CarburantCardService $carburantCardService,
        ChauffeurService $chauffeurService,
        VehiculeService $vehiculeService,
        TarifService $tarifService
    ) {
        $this->reservationService = $reservationService;
        $this->tresorerieService = $tresorerieService;
        $this->clientService = $clientService;
        $this->voyageService = $voyageService;
        $this->remorqueService = $remorqueService;
        $this->carburantCardService = $carburantCardService;
        $this->chauffeurService = $chauffeurService;
        $this->vehiculeService = $vehiculeService;
        $this->tarifService = $tarifService;
    }
    public function index(Request $request)
    {
        $filtre = ExtractFiltreReservation::extractFilter($request);
        $output = $this->reservationService->getAll($filtre);
        $clients = $this->clientService->getAll([]);
        $lieux_chargement = $this->reservationService->getDistinctLieuChargement();
        $lieux_livraison = $this->reservationService->getDistinctLieuLivraison();

        return Inertia::render('Reservation/Index', [
            "data" => $output,
            "filters" => [],
            "clients" => $clients,
            "lieux_chargement" => $lieux_chargement,
            "lieux_livraison" => $lieux_livraison,
            "flash" => session('flash', [])
        ]);
    }

    public function show(Reservation $reservation)
    {
        //charger les voyages (fictif et déjà present pour ce resecvation
        $voyages = $this->reservationService->prepareVoyagesForReservation($reservation);
        // Charger les véhicules avec leurs relations chauffeurs et remorque
        $vehicules = $this->vehiculeService->getVehiculesWithChauffeursAndRemorque();
        // Charger toutes les remorques disponibles
        $remorques = $this->remorqueService->getAll([]);
        // Charger tous les chauffeurs disponibles
        $chauffeurs = $this->chauffeurService->getMappedChauffeurs();
        $immobilisation_value = Immobilisation::first()?->valeur;
        $carburantCards = $this->carburantCardService->getMappedCarburantCards();
        $all_lieu_options = $this->voyageService->getAllLieuOptions();
        $tariffs_with_details = $this->tarifService->getTarifsWithDetailsMapped();
        $tresoreries = $this->tresorerieService->getAll([]);

        return back()->with([
            'flash' => [
                'data' => $reservation,
                'voyages' => $voyages,
                'carburant_cards' => $carburantCards,
                'all_tariffs_with_details' => $tariffs_with_details,
                'lieu_livraison_options' => $all_lieu_options,
                'tresoreries' => $tresoreries,
                'vehicules' => $vehicules, // Ajouter les véhicules avec leurs relations
                'remorques' => $remorques, // Ajouter toutes les remorques disponibles
                'chauffeurs' => $chauffeurs, // Ajouter tous les chauffeurs disponibles
                'immobilisation_value' => $immobilisation_value,
            ]
        ]);
    }

    public function generatenumero()
    {
        $output = $this->reservationService->generateNumeroReservation();

        $lieu_options = $this->reservationService->getAllLieuOptions();
        return back()->with([
            'flash' => ['data' => $output, 'lieu_options' => $lieu_options]
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date_reservation' => 'required|date',
            'lieu_chargement' => 'required|string|max:255',
            'lieu_livraison' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'nbr_vehicule' => 'required|integer|min:1',
            'etat_facture' => 'required|in:valide,non_valide',
            'commentaire' => 'nullable|string',
        ]);

        $output = $this->reservationService->create($validatedData);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
    public function update(Request $request, Reservation $reservation)
    {
        $validatedData = $request->validate([
            'date_reservation' => 'required|date',
            'lieu_chargement' => 'required|string|max:255',
            'lieu_livraison' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'nbr_vehicule' => 'required|integer|min:1',
            'etat_facture' => 'required|in:valide,non_valide',
            'commentaire' => 'nullable|string',
        ]);

        $output = $this->reservationService->update($reservation, $validatedData);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
    public function destroy(Reservation $reservation)
    {
        $this->reservationService->delete($reservation);
        return back()->with('message.success', 'Réservation supprimée avec succès.');
    }
}
