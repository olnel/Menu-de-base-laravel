<?php

namespace App\Http\Controllers;

use App\Models\Remorque;
use App\Models\Vehicule;
use App\Services\ParamElementService;
use App\Services\PlanningCalendarService;
use App\Services\RemorqueDocumentService;
use App\Services\RemorqueElementService;
use App\Services\RemorquePhotoService;
use App\Services\RemorqueService;
use App\Services\DocumentDynamicService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreDocumentVehicule;
use App\Utils\ExtractFiltrePhotoVehicule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RemorqueController extends Controller
{
    private $service;
    private ParamElementService $paramElementService;
    private RemorqueElementService $remorqueElementService;
    private RemorquePhotoService $remorque_photo_service;
    private RemorqueDocumentService $remorque_document_service;
    private PlanningCalendarService $planningCalendarService;
    private DocumentDynamicService $documentDynamicService;

    public function __construct(RemorqueService $remorqueService, ParamElementService $paramElementService,
                                 RemorqueElementService $remorqueElementService,
                                RemorquePhotoService $remorquePhotoService, RemorqueDocumentService $remorqueDocumentService,
                                PlanningCalendarService $planningCalendarservice, DocumentDynamicService $documentDynamicService
    )
    {
        $this->paramElementService = $paramElementService;
        $this->service = $remorqueService;
        $this->remorqueElementService = $remorqueElementService;
        $this->remorque_photo_service = $remorquePhotoService;
        $this->remorque_document_service = $remorqueDocumentService;
        $this->planningCalendarService = $planningCalendarservice;
        $this->documentDynamicService = $documentDynamicService;

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $element_vehicule = $this->paramElementService->getAll();

        $requiredDocs = $this->documentDynamicService->getRequiredDocumentModels(Remorque::class);

        return Inertia::render("Remorque/Index", [
            "data" => $output,
            'liste_modele' => $this->service->fetchDistinctColumn('modele_remorque'),
            'liste_marque' => $this->service->fetchDistinctColumn('marque_remorque'),
            'required_documents' => $requiredDocs,
            "filters" => [
                "search" => $request->search,
            ],
            "flash" => [
                'element_vehicule' => $element_vehicule ,
                'data' => session('flash.data') ?? []
            ]
        ]);
    }


    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'numero_remorque' => 'required|string|unique:remorques,numero_remorque',
            'modele_remorque'=> 'nullable|string',
            'marque_remorque'=> 'nullable|string',
            'param_element_id' => 'nullable',
            'element_remorques' => 'nullable|array',
            'element_remorques.*.emplacement' => 'nullable|string',
            'element_remorques.*.libelle' => 'nullable|string',
            'element_remorques.*.reference' => 'nullable|string',
            'element_remorques.*.numero_serie' => 'nullable|string',
            'element_remorques.*.etat_piece' => 'nullable|string',
            'element_remorques.*.is_pneu' => 'nullable|boolean',
            'element_remorques.*.is_first' => 'nullable|boolean',
            'element_remorques.*.date_montage' => 'nullable|date',
            'documents' => 'nullable|array',
            'documents.*.document_type_id' => 'required|exists:document_types,id',
            'documents.*.fichier' => 'nullable|file|max:10240',
            'documents.*.date_expiration' => 'nullable|date',
            'documents.*.observation' => 'nullable|string',
        ]);

        $output= $this->service->create($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message'],
        ) ->with('flash.data', ['form_data' => $request->all()]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicule $vehicule)
    {
        $output = $this->service->getVehiculeDetails($vehicule->id);

        return back()->with([
            'flash' => [
                'data' => $output['data']
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
    public function update(Request $request, Remorque $remorque)
    {
        $validated = $request->validate([
            'numero_remorque' => 'required|string|unique:remorques,numero_remorque,'.$remorque->id,
            'modele_remorque'=> 'nullable|string',
            'marque_remorque'=> 'nullable|string',
            'param_element_id' => 'nullable',
            'element_remorques' => 'sometimes|nullable|array',
            'element_remorques.*.emplacement' => 'sometimes|nullable|string',
            'element_remorques.*.libelle' => 'sometimes|nullable|string',
            'element_remorques.*.reference' => 'sometimes|nullable|string',
            'element_remorques.*.numero_serie' => 'sometimes|nullable|string',
            'element_remorques.*.etat_piece' => 'sometimes|nullable|string',
            'element_remorques.*.is_pneu' => 'sometimes|nullable|boolean',
            'element_remorques.*.is_first' => 'sometimes|nullable|boolean',
            'element_remorques.*.date_montage' => 'sometimes|nullable|date',
            'documents' => 'nullable|array',
            'documents.*.document_type_id' => 'required|exists:document_types,id',
            'documents.*.fichier' => 'nullable|file|max:10240',
            'documents.*.date_expiration' => 'nullable|date',
            'documents.*.observation' => 'nullable|string',
        ]);

        $output= $this->service->update($remorque, $validated);

        $vehiculeData = $this->service->getVehiculeDetails($remorque->id);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        )->with('flash.data', array_merge($vehiculeData['data'], ['form_data' => $request->all()]));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicule $vehicule)
    {
        try {
            $output = $this->service->delete($vehicule);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }

    public function details(Remorque $remorque, Request $request)
    {

        $output = $this->service->getVehiculeDetails($remorque->id);
        $vehicule_element = $this->remorqueElementService->getElementsByVehicule($remorque->id);
        $filtre = ExtractFiltrePhotoVehicule::extractFilter($request);
        $filtreDocument= ExtractFiltreDocumentVehicule::extractFilter($request);
        if (!isset($filtre['search'])) {
            $filtre['search'] = '';
        }


        $vehiculePhoto = $this->remorque_photo_service->getAll($filtre);
        $vehiculeDocuments = $this->remorque_document_service->getAll($filtreDocument);
        $element_vehicule = $this->paramElementService->getAll();
        $list_emplacements = $this->remorqueElementService->fetchDistinctEmplacement();



        return Inertia::render("Remorque/Information/Index", [
            "info" => $output,
            "elements" => $vehicule_element,
            'LIST_ELEMENT_VEHICULE' => $element_vehicule,
            'liste_modele' => $this->service->fetchDistinctColumn('marque_remorque'),
            'liste_modele_remorque' => $this->service->fetchDistinctColumn('modele_remorque'),
            'liste_couleur' => $this->service->fetchDistinctColumn(),
            "liste_photo" => $vehiculePhoto,
            "list_emplacements" => $list_emplacements,
            "documents" => $vehiculeDocuments,
            'flash' => [
                'data' => session('flash.data') ?? []
            ]
        ]);
    }
}
