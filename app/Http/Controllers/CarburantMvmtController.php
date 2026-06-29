<?php

namespace App\Http\Controllers;
use App\Models\Chauffeur;
use App\Models\Vehicule;
use App\Models\CarburantMouvement;
use App\Models\User;
use App\Models\CarburantCard;
use App\Services\CarburantCardService;
use App\Services\CarburantMouvementService;
use App\Services\ChauffeurService;
use App\Services\UserService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreCarburantTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CarburantMvmtController extends Controller
{
    private $service;
    private $carburantcardservice;
    private $vehiculeService;
    private $chauffeurService;
    private $userService;

    public function __construct(CarburantMouvementService $cardMouvementService,CarburantCardService $carburantCardService,VehiculeService $vehiculeService,ChauffeurService $chauffeurService,UserService $userService)
    {
        $this->service = $cardMouvementService;
        $this->carburantcardservice = $carburantCardService;
        $this->vehiculeService = $vehiculeService;
        $this->chauffeurService = $chauffeurService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $filtre = ExtractFiltreCarburantTransaction::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $carburantCards = $this->carburantcardservice->getMappedCarburantCards();
        $vehicules = $this->vehiculeService->getAll([]);
        $chauffeurs = $this->chauffeurService->getMappedChauffeurs();
        $users = $this->userService->getAll([]);
        return Inertia::render('CarburantMouvement/Index', [
            "data" => $output,
            "filters" => [],
            "users" => $users,
            "carburantCards" => $carburantCards,
            "vehicules" => $vehicules,
            "chauffeurs" => $chauffeurs,
            "flash" => session('flash', [])
        ]);
    }

    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'montant' => 'required|numeric|min:0',
    //         'date_mvmt' => 'required|date',
    //         'carburant_card_id' => 'required_if:type,achat_carte|exists:carburant_cards,id',
    //         'type' => ['required', Rule::in(['achat_carte', 'achat_espece'])],
    //         'chauffeur_id' => 'nullable|exists:chauffeurs,id',
    //         'vehicule_id' => 'nullable|exists:vehicules,id',

    //         'commentaire' => 'string',
    //         'piece_jointe' => 'nullable|array',
    //     ]);
    //     $validatedData['user_id'] = Auth::id();
    //     $output = $this->service->create($validatedData);
    //     if (!empty($output['error'])) {
    //         return back()->with('message.error', $output['message']);

    //     }
    //     return back()->with('message.success', $output['message']);
    // }

    public function update(Request $request, CarburantMouvement $cardMouvement)
    {

    }

    // public function destroy(CarburantMouvement $cardMouvement)
    // {
    //     $output = $this->service->delete($cardMouvement);

    //     if (!empty($output['error'])) {
    //         return back()->with('message.error', $output['message']);
    //     }

    //     return back()->with('message.success', $output['message']);
    // }
}
