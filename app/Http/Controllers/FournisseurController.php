<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFournisseurRequest;
use App\Http\Requests\UpdateFournisseurRequest;
use App\Models\Fournisseur;
use App\Services\FournisseurService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FournisseurController extends Controller
{
    private FournisseurService $service;
    public function __construct(FournisseurService $fournisseurService)
    {
        $this->service = $fournisseurService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("Tiers/Fournisseur/Index", [
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
    public function store(StoreFournisseurRequest $request)
    {
        $output= $this->service->create($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Fournisseur $fournisseur)
    {
        return back()->with([
            'flash' => [
                'data' => $fournisseur
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
    public function update(UpdateFournisseurRequest $request, Fournisseur $fournisseur)
    {
        $output= $this->service->update($fournisseur ,$request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fournisseur $fournisseur)
    {
        try {
            $output = $this->service->delete($fournisseur);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
