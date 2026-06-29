<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use App\Services\ParamElementService;
use App\Services\PlanningCalendarService;
use App\Services\PneuSerieService;
use App\Services\RemorqueService;
use App\Services\VehiculeDocumentService;
use App\Services\VehiculeElementService;
use App\Services\VehiculePhotoService;
use App\Services\VehiculeService;
use App\Services\DocumentDynamicService;
use App\Http\Requests\StoreVehiculeRequest;
use App\Http\Requests\UpdateVehiculeRequest;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreDocumentVehicule;
use App\Utils\ExtractFiltrePhotoVehicule;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehiculeController extends Controller
{
    private $service;
    private ParamElementService $paramElementService;
    private VehiculeElementService $vehiculeElementService;
    private VehiculePhotoService $vehicule_photo_service;
    private VehiculeDocumentService $vehicule_document_service;
    private PlanningCalendarService $planningCalendarService;
    private RemorqueService $remorqueService;
    private PneuSerieService $pneu_serieservice;
    private DocumentDynamicService $documentDynamicService;

    public function __construct(VehiculeService $vehiculeService, ParamElementService $paramElementService,
                                VehiculeElementService $vehiculeElementService,
                                VehiculePhotoService $vehiculePhotoService, VehiculeDocumentService $vehiculeDocumentService,
                                PlanningCalendarService $planningCalendarservice, RemorqueService $remorqueService,
                                PneuSerieService $pneuSerieService, DocumentDynamicService $documentDynamicService
    )
    {
        $this->paramElementService = $paramElementService;
        $this->service = $vehiculeService;
        $this->vehiculeElementService = $vehiculeElementService;
        $this->vehicule_photo_service = $vehiculePhotoService;
        $this->vehicule_document_service = $vehiculeDocumentService;
        $this->planningCalendarService = $planningCalendarservice;
        $this->remorqueService = $remorqueService;
        $this->pneu_serieservice = $pneuSerieService;
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
        $planning_notification = $this->planningCalendarService->getFormattedNotifications();;
        $remorques = $this->remorqueService->getAll([]);
        $list_pneuu = $this->pneu_serieservice->getPneusNonAssignes();
        $user = auth()->user();

        $requiredDocs = $this->documentDynamicService->getRequiredDocumentModels(Vehicule::class);

        if (!$user->isDNA() && !in_array('vehicule.index', $user->accepted_routes)) {
            return redirect()->route('calendrier.index');
        }

        return Inertia::render("Vehicule/Index", [
            "data" => $output,
            'liste_modele' => $this->service->fetchDistinctColumn('modele'),
            'liste_marque' => $this->service->fetchDistinctColumn('marque'),
            'liste_couleur' => $this->service->fetchDistinctColumn(),
            'liste_remorque' => $remorques,
            'notification_data' => $planning_notification,
            'required_documents' => $requiredDocs,
            "filters" => [
                "search" => $request->search,
            ],
            "flash" => [
                'element_vehicule' => $element_vehicule ,
                'liste_pneu_numero_serie' => $list_pneuu,
                'data' => session('flash.data') ?? []
            ]
        ]);
    }


    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreVehiculeRequest $request)
    {
        $output = $this->service->create($request->validated());

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
    public function update(UpdateVehiculeRequest $request, Vehicule $vehicule)
    {
        $output = $this->service->update($vehicule, $request->validated());

        $vehiculeData = $this->service->getVehiculeDetails($vehicule->id);

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

    public function details(Vehicule $vehicule, Request $request)
    {

        $output = $this->service->getVehiculeDetails($vehicule->id);
        $vehicule_element = $this->vehiculeElementService->getElementsByVehicule($vehicule->id);
        $filtre = ExtractFiltrePhotoVehicule::extractFilter($request);
        $filtreDocument= ExtractFiltreDocumentVehicule::extractFilter($request);
        if (!isset($filtre['search'])) {
            $filtre['search'] = '';
        }


        $vehiculePhoto = $this->vehicule_photo_service->getAll($filtre);
        $vehiculeDocuments = $this->vehicule_document_service->getAll($filtreDocument);
        $element_vehicule = $this->paramElementService->getAll();
        $list_emplacements = $this->vehiculeElementService->fetchDistinctEmplacement();
        $remorques = $this->remorqueService->getAll([]);
        return Inertia::render("Vehicule/Information/Index", [
            "info" => $output,
            "elements" => $vehicule_element,
            'LIST_ELEMENT_VEHICULE' => $element_vehicule,
            'liste_modele' => $this->service->fetchDistinctColumn('modele'),
            'liste_marque' => $this->service->fetchDistinctColumn('marque'),
            'liste_couleur' => $this->service->fetchDistinctColumn(),
            "liste_photo" => $vehiculePhoto,
            "list_emplacements" => $list_emplacements,
            'liste_remorque' => $remorques,
            "documents" => $vehiculeDocuments,
            'flash' => [
                'data' => session('flash.data') ?? []
            ]
        ]);
    }

    public function fetchNumeroSerie(Request $request)
    {
        $search = $request->input('search');
        $data = $this->pneu_serieservice->getPneusNonAssignes($search);

        return back()->with([
            'flash' => [
                'data' => $data
            ]
        ]);
    }
}
