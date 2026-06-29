<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTarifRequest;
use App\Http\Requests\UpdateTarifRequest;
use App\Models\Tarif;
use App\Services\TarifService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    private TarifService $service;
    public function __construct(TarifService $tarifService)
    {
        $this->service = $tarifService;
    }
    public function index(Request $request)
    {

        $filtre = ExtractFiltre::extractFilter($request);

        $output = $this->service->getAll($filtre);

        return inertia("Tarif/Index", [
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
    public function store(StoreTarifRequest $request)
    {

        $output = $this->service->saveWithDetais($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Tarif $tarif)
    {

        $tarif->load('details');
        return back()->with([
            'flash' => [
                'data' => $tarif
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
    public function update(UpdateTarifRequest $request, Tarif $tarif)
    {
        $output = $this->service->updateWithDetails($tarif, $request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarif $tarif)
    {
        $output = $this->service->deleteWithDetails($tarif);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
