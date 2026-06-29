<?php

namespace App\Http\Controllers;

use App\Services\CarburantTransactionService;
use Illuminate\Http\Request;
use App\Models\Chauffeur;
use App\Models\Vehicule;
use App\Models\User;
use App\Models\CarburantCard;
use App\Models\CarburantTransaction;
use App\Services\CarburantCardService;
use App\Services\ChauffeurService;
use App\Services\UserService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreCarburantTransaction;
use Illuminate\Validation\Rule;

use Inertia\Inertia;


class CarburantTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $service;
    private $carburantcardService;
    private $vehiculeService;
    private $chauffeurService;
    private $userService;
    public function __construct(CarburantTransactionService $transactionService, CarburantCardService $carburantCardService, VehiculeService $vehiculeService,ChauffeurService $chauffeurService,UserService $userService)
    {
        $this->service = $transactionService;
        $this->carburantcardService = $carburantCardService;
        $this->vehiculeService = $vehiculeService;
        $this->chauffeurService = $chauffeurService;
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $filtre = ExtractFiltreCarburantTransaction::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $carburantCards = $this->carburantcardService->getMappedCarburantCards();
        $vehicules = $this->vehiculeService->getAll([]);
        $chauffeurs = $this->chauffeurService->getMappedChauffeurs();
        $users = $this->userService->getAll([]);
        return Inertia::render('CarburantTransaction/Index', [
            "data" => $output,
            "filters" => [],
            "users" => $users,
            "carburantCards" => $carburantCards,
            "vehicules" => $vehicules,
            "chauffeurs" => $chauffeurs,
            "flash" => session('flash', [])
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric',
            'carburant_litre' => 'nullable|numeric',
            'prix_unitaire' => 'nullable|numeric',
            'date_transaction' => 'required|date',
            'reference' => 'required',
            'carburant_card_id' => 'nullable|required_if:type,achat_carte|exists:carburant_cards,id',
            'type' => ['required', Rule::in(['achat_carte', 'achat_espece'])],
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'commentaire' => 'string|nullable',
            'piece_jointe' => 'nullable|array',
            'voyage_id' => 'nullable|exists:voyages,id',
        ]);
        $output = $this->service->create($validatedData);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }
        return back()->with('message.success', $output['message']);
    }

    public function update(Request $request, CarburantTransaction $carburantTransaction)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric',
            'carburant_litre' => 'nullable|numeric',
            'prix_unitaire' => 'nullable|numeric',
            'date_transaction' => 'required|date',
            'reference' => 'required',
            'carburant_card_id' => 'nullable|required_if:type,achat_carte|exists:carburant_cards,id',
            'type' => ['required', Rule::in(['achat_carte', 'achat_espece'])],
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'vehicule_id' => 'nullable|exists:vehicules,id',
            'commentaire' => 'string|nullable',
            'piece_jointe' => 'nullable|array',
            'existing_files' => 'nullable',
            'voyage_id' => 'nullable|exists:voyages,id',
        ]);

        $output = $this->service->update($carburantTransaction->id, $validatedData);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }
        return back()->with('message.success', $output['message']);
    }

    public function destroy(CarburantTransaction $carburantTransaction)
    {
        $output = $this->service->delete($carburantTransaction);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }

        return back()->with('message.success', $output['message']);
    } /**
      * Display the specified resource.
      */
    public function show(string $id)
    {

    }
}
