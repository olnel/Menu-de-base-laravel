<?php

namespace App\Http\Controllers;

use App\Exports\ExportDataexcel;
use App\Http\Requests\StoreTresorerieMouvementRequest;
use App\Models\Camp;
use App\Models\TresorerieMouvement;
use App\Services\PosteDepenseService;
use App\Services\TresorerieMouvementService;
use App\Services\TresorerieService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractTresorerieMouvement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TresorerieMouvementController extends Controller
{
    private TresorerieMouvementService $service;
    private TresorerieService $tresorerieService;
    public function __construct(TresorerieMouvementService $tresorerieMouvementService, TresorerieService $tresorerieService, private readonly PosteDepenseService $posteDepenseService)
    {
        $this->service = $tresorerieMouvementService;
        $this->tresorerieService = $tresorerieService;
    }

    public function index(Request $request)
    {
        $filtre = ExtractTresorerieMouvement::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $posteDepenses = $this->posteDepenseService->getAll([]);
        $tresorerie = $this->tresorerieService->getAll([]);

        return Inertia::render("ModuleTresorerie/TresorerieMouvement/Index", [
            "data" => $output,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', []),
            "tresoreries" => $tresorerie,
            'posteDepenses' => $posteDepenses,
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
    public function store(StoreTresorerieMouvementRequest $request)
    {

        $output= $this->service->createMouvement($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(TresorerieMouvement $tresorerie_mouvement)
    {
        return back()->with([
            'flash' => [
                'data' => $tresorerie_mouvement
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
     *
     */
    public function update(StoreTresorerieMouvementRequest $request, TresorerieMouvement $tresorerie_mouvement)
    {
        $validated = $request->validated();
        $output= $this->service->updateMouvement($tresorerie_mouvement, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TresorerieMouvement $tresorerie_mouvement)
    {
        try {
            $output = $this->service->deleteMouvement($tresorerie_mouvement);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }

    public function export(Request $request)
    {
        $request->validate([
            "exportable" => "required"
        ]);
        $exportable = json_decode($request->exportable);


        return (new ExportDataexcel($exportable, TresorerieMouvement::filter()->get(),'tresorerie_mouvement'))->download();
    }
}
