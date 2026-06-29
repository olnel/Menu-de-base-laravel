<?php

namespace App\Http\Controllers;
use App\Models\CarburantCard;
use App\Models\Chauffeur;
use App\Models\Vehicule;
use App\Models\Magasin;
use App\Services\CarburantCardService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreCarburantCard;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CarburantCardController extends Controller
{
    private $service;

    public function __construct(CarburantCardService $carburantcardService)
    {
        $this->service = $carburantcardService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreCarburantCard::extractFilter($request);
        $output = $this->service->getAll($filtre);
        // $vehicules = Vehicule::all()->map(function ($vehicule) {
        //     return [
        //         'value' => $vehicule->id,
        //         'label' => $vehicule->marque . ' - ' . $vehicule->modele . ' (' . $vehicule->immatriculation . ')',
        //     ];
        // });
        // $chauffeurs = Chauffeur::all()->map(function ($chauffeur) {
        //     return [
        //         'value' => $chauffeur->id,
        //         'label' => $chauffeur->nom . ' ' . $chauffeur->prenom,
        //     ];
        // });
        return Inertia::render("CarburantCard/Index", [
            "data" => $output,
            "filters" => [],
            // "vehicules" => $vehicules,
            // "chauffeurs" => $chauffeurs,
            "flash" => session('flash', [])
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_carte' => [
                'required',
                'string',
                'max:255',
                Rule::unique('carburant_cards')->whereNull('deleted_at'),
            ],
            'plafond_mensuel' => 'required|numeric|min:0',
            'active' => 'required|boolean',
        ]);
        $output = $this->service->create($validated);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }
        return back()->with('message.success', $output['message']);
    }


    public function show(CarburantCard $carburantCard)
    {
        return back()->with([
            'flash' => [
                'data' => $carburantCard
            ]
        ]);
    }


    public function updateCardStatus(Request $request, int $carburantCardID)
    {
        $carburantCard = CarburantCard::findOrFail($carburantCardID);
        $output = $this->service->updateStatus($carburantCard);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }
        return back()->with('message.success', $output['message']);
    }


    public function update(Request $request, CarburantCard $carburantCard)
    {
        $validated = $request->validate([
            'numero_carte' => [
                'required',
                'string',
                'max:255',
                Rule::unique('carburant_cards')
                    ->whereNull('deleted_at')
                    ->ignore($carburantCard->id),
            ],
            'plafond_mensuel' => 'required|numeric|min:0',
            'active' => 'required|boolean',
        ]);
        $output = $this->service->update($carburantCard, $validated);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }
        return back()->with('message.success', $output['message']);
    }


    public function destroy(CarburantCard $carburantCard)
    {
        $output = $this->service->delete($carburantCard);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }
        return back()->with('message.success', $output['message']);
    }


    public function HandleRechargeCard(Request $request)
    {
        $validated = $request->validate([
            'card_ids' => ['required', 'array', 'min:1'],
            'card_ids.*' => [
                'required',
                'integer',
                Rule::exists('carburant_cards', 'id')->where(function ($query) {
                    $query->where('active', true);
                })
            ],
            'montant' => ['required', 'numeric', 'min:0'],
            'motif' => ['nullable', 'string', 'max:255'],
        ]);
        $cardIds = $validated['card_ids'];
        $amount = $validated['montant'];
        $motif = $validated['motif'];
        $userId = Auth::id();
        $output = $this->service->rechargeCarburantCards($cardIds, $amount, $motif, $userId);
        if (!empty($output['error'])) {
            return back()->with('message.error', $output['message']);
        }
        return redirect()->back()->with('message.success', 'recharge effectuer avec succès');
    }

}
