<?php

namespace App\Http\Controllers;

use App\Models\Immobilisation;
use App\Services\ImmobilisationService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImmobilisationController extends Controller
{
    private $service;
    public function __construct(ImmobilisationService $immobilisationService)
    {
        $this->service = $immobilisationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("Immobilisation/Index", [
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Immobilisation $immobilisation)
    {
        return back()->with([
            'flash' => [
                'data' => $immobilisation
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
    public function update(Request $request, Immobilisation $immobilisation)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|unique:immobilisations,libelle,'.$immobilisation->id,
            'valeur'=> 'required|numeric',

        ]);

        $output= $this->service->update($immobilisation ,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
