<?php

namespace App\Http\Controllers;

use App\Models\PrimeConfig;
use App\Services\PrimeConfigService;
use App\Services\TypeSalarieService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PrimeConfigController extends Controller
{
    private $service;
    private $typeSalarieService;

    public function __construct(PrimeConfigService $primeConfigService, TypeSalarieService $typeSalarieService)
    {
        $this->service = $primeConfigService;
        $this->typeSalarieService = $typeSalarieService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $data = $this->service->getAll($filtre);
        $types = $this->typeSalarieService->getAll([]);

        return Inertia::render("PrimeConfig/Index", [
            "data" => $data,
            "types_salarie" => $types,
            "filters" => [
                "search" => $request->search
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'type_salarie_id' => 'nullable|exists:type_salaries,id',
            'is_global' => 'boolean',
            'is_per_voyage' => 'boolean',
            'is_actif' => 'boolean',
        ]);

        $output = $this->service->create($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrimeConfig $primeConfig)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'type_salarie_id' => 'nullable|exists:type_salaries,id',
            'is_global' => 'boolean',
            'is_per_voyage' => 'boolean',
            'is_actif' => 'boolean',
        ]);

        $output = $this->service->update($primeConfig, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrimeConfig $primeConfig)
    {
        $output = $this->service->delete($primeConfig);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
