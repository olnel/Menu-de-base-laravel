<?php

namespace App\Http\Controllers;

use App\Models\LibelleMaintenance;
use App\Models\PlanningCalendar;
use App\Services\PlanningCalendarService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanningCalendarController extends Controller
{
    private $service;
    private $vehiculeService;
    public function __construct(PlanningCalendarService $planningCalendarService, VehiculeService $vehiculeService)
    {
        $this->service = $planningCalendarService;
        $this->vehiculeService = $vehiculeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $vehiculeListes = $this->vehiculeService->getAll(array())->toArray();
        $libelleMaintenance = LibelleMaintenance::get()->toArray();

        return Inertia::render("PlanningCalendar/Index", [
            "data" => $output,
            "libelleMaintenances"=>$libelleMaintenance,
            'vehiculeListes' => $vehiculeListes,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => [
                'data' => session('flash.data') ?? []
            ]
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
            'vehicule_id' => 'required|numeric',
            'libelle' => 'required|string',
            'notification' => 'required',
            'background' => 'required|string',
            'text_color' => 'required|string',
            'date_maintenance' => 'required|date'
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
    public function show(PlanningCalendar $planning_calendar)
    {
        return back()->with([
            'flash' => [
                'data' =>$planning_calendar
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
    public function update(Request $request, PlanningCalendar $planning_calendar)
    {
        $validated = $request->validate([
            'vehicule_id' => 'required|numeric',
            'libelle' => 'required|string',
            'notification' => 'required',
            'background' => 'required|string',
            'text_color' => 'required|string',
            'date_maintenance' => 'required|date'
        ]);

        $output = $this->service->update($planning_calendar,$validated);
        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanningCalendar $planning_calendar)
    {
        try {
            $output = $this->service->delete($planning_calendar);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
