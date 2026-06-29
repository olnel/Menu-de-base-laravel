<?php

namespace App\Http\Controllers;

use App\Models\RemorqueDocument;
use App\Models\VehiculeDocument;
use App\Repositories\VehiculeDocumentRepository;
use App\Services\RemorqueDocumentService;
use App\Services\VehiculeDocumentService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltreDocumentVehicule;
use App\Utils\ForamatDate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RemorqueDocumentController extends Controller
{
    protected $service;
    private VehiculeService $vehiculeService;
    public function __construct(RemorqueDocumentService $vehiculeDocumentService,VehiculeService $vehiculeService )
    {
        $this->service = $vehiculeDocumentService;
        $this->vehiculeService = $vehiculeService;
    }
    public function index(Request $request)
    {
        $filtreDocument= ExtractFiltreDocumentVehicule::extractFilter($request);
        $vehiculeDocuments = $this->service->getAll($filtreDocument);
        $vehicules = $this->vehiculeService->getAll([]);

        return Inertia::render("RemorqueDocument/Index", [
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
            'nom_document' => 'required|string|unique:remorque_documents,nom_document',
            'description' => 'nullable|string',
            'fichier_jointe' => 'nullable|array',
            'remorque_id' => 'required'
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
    public function show(RemorqueDocument $remorquedocument)
    {

        return back()->with([
            'flash' => [
                'data' => $remorquedocument->toArray()
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
    public function update(Request $request, RemorqueDocument $remorquedocument)
    {

        $validated = $request->validate([
            'date_expiration' => 'nullable',
            'nom_document' => 'required|string|unique:remorque_documents,nom_document,'.$remorquedocument->id,
            'description' => 'nullable|string',
            'fichier_jointe' => 'nullable',
            'existing_files' => 'nullable',
            'remorque_id' => 'required'
        ]);


        $output= $this->service->update($remorquedocument,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RemorqueDocument $remorquedocument)
    {

        try {
            $output = $this->service->delete($remorquedocument);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
