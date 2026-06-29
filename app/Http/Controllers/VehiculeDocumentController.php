<?php

namespace App\Http\Controllers;

use App\Models\VehiculeDocument;
use App\Repositories\VehiculeDocumentRepository;
use App\Services\VehiculeDocumentService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltreDocumentVehicule;
use App\Utils\ForamatDate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehiculeDocumentController extends Controller
{
    private $service;
    private VehiculeService $vehiculeService;
    public function __construct(VehiculeDocumentService $vehiculeDocumentService , VehiculeService $vehiculeService)
    {
        $this->service = $vehiculeDocumentService;
        $this->vehiculeService = $vehiculeService;
    }
    public function index(Request $request)
    {
        $filtreDocument= ExtractFiltreDocumentVehicule::extractFilter($request);
        $vehiculeDocuments = $this->service->getAll($filtreDocument);
        $vehicules = $this->vehiculeService->getAll([]);

        return Inertia::render("VehiculeDocument/Index", [
            "data" => $vehiculeDocuments,
            "vehicules" => $vehicules,
            'flash' => [
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
            'date_expiration' => 'nullable',
            'nom_document' => 'required|string|unique:vehicule_documents,nom_document',
            'description' => 'nullable|string',
            'fichier_jointe' => 'required|array',
            'immatriculation' => 'required'
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
    public function show(VehiculeDocument $vehiculedocument)
    {

        return back()->with([
            'flash' => [
                'data' => $vehiculedocument->toArray()
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
    public function update(Request $request, VehiculeDocument $vehiculedocument)
    {

        $validated = $request->validate([
            'date_expiration' => 'nullable',
            'nom_document' => 'required|string|unique:vehicule_documents,nom_document,'.$vehiculedocument->id,
            'description' => 'nullable|string',
            'fichier_jointe' => 'nullable',
            'existing_files' => 'nullable',
            'immatriculation' => 'required'
        ]);


        $output= $this->service->update($vehiculedocument,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehiculeDocument $vehiculedocument)
    {

        try {
            $output = $this->service->delete($vehiculedocument);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
