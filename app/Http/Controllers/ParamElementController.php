<?php

namespace App\Http\Controllers;

use App\Models\ParamElement;
use App\Services\ParamElementService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParamElementController extends Controller
{
    private ParamElementService $service;

    /**
     * Injection de dépendence du service
     * @param ParamElementService $paramElementService
     */
    public function __construct(ParamElementService $paramElementService)
    {
        $this->service = $paramElementService;
    }

    /**
     * Affiche la liste des éléments avec pagination et filtres
     *
     * @param Request $request Requête HTTP contenant les filtres
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("Parametre/ElementVehicule/Index", [
            "data" => $output,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', [])
        ]);
    }


    /**
     * Stocke un nouvel element avec ces détails
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'type_model' => 'required|string|unique:param_elements,type_model',
            'details' => 'required|array|min:1',
            'details.*.emplacement' => 'required|string',
            'details.*.libelle' => 'required|string',
            'details.*.reference' => 'string|nullable',
            'details.*.is_pneu' => 'sometimes|boolean',
        ]);

        $output= $this->service->create($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Affiche un élément spécifique avec ses détails
     *
     * @param ParamElement $paramelement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(ParamElement $paramelement)
    {
        $paramelement->load('details');
        return back()->with([
            'flash' => [
                'data' => $paramelement
            ]
        ]);
    }

    /**
     * Met à jour un élément existant
     *
     * @param Request $request
     * @param ParamElement $paramelement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ParamElement $paramelement)
    {
        $validated = $request->validate([
            'id' => 'required',
            'type_model' => 'required|string|unique:param_elements,type_model,'.$paramelement->id,
            'details' => 'required|array|min:1',
            'details.*.emplacement' => 'required|string',
            'details.*.libelle' => 'required|string',
            'details.*.reference' => 'string|nullable',
            'details.*.is_pneu' => 'sometimes|boolean',
        ]);

        $output= $this->service->update($paramelement, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );

    }


    /**
     * Supprime un élément et ses détails associés
     *
     * @param ParamElement $paramelement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ParamElement $paramelement)
    {
        try {
            $output = $this->service->delete($paramelement);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
