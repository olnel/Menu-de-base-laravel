<?php

namespace App\Http\Controllers;

use App\Models\LibelleMaintenance;
use App\Services\LibelleMaintenanceService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LibelleMaintenanceController extends Controller
{
    protected $service;

    public function __construct(LibelleMaintenanceService $libelleMaintenanceService)
    {
        $this->service = $libelleMaintenanceService;
    }

    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("Parametre/LibelleMaintenance/Index", [
            "data" => $output,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', [])
        ]);
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

            'libelle' => 'required|string|unique:libelle_maintenances,libelle',
            'notification' => 'required',
            'background' => 'required|string',
            'text_color' => 'required|string',

        ]);

        $output= $this->service->create($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(LibelleMaintenance $libelle_maintenance)
    {
        return back()->with([
            'flash' => [
                'maintenance' => $libelle_maintenance
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LibelleMaintenance $libelle_maintenance)
    {
        $validated = $request->validate([

            'libelle' => 'required|string|unique:libelle_maintenances,libelle,'.$libelle_maintenance->id,
            'notification' => 'required',
            'background' => 'required|string',
            'text_color' => 'required|string',

        ]);

        $output= $this->service->update($libelle_maintenance ,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LibelleMaintenance $libelle_maintenance)
    {
        try {
            $output = $this->service->delete($libelle_maintenance);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
