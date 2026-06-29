<?php

namespace App\Http\Controllers;

use App\Services\TresorerieFluxService;
use App\Services\TresorerieService;
use App\Utils\ExtractTresorerieMouvement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TresorerieFluxController extends Controller
{
    private $service;
    private TresorerieService $tresorerieService;
    public function __construct(TresorerieFluxService $tresorerieFluxService, TresorerieService $tresorerieService)
    {
        $this->tresorerieService = $tresorerieService;
        $this->service = $tresorerieFluxService;
    }
    public function index(Request $request)
    {

        $filtre = ExtractTresorerieMouvement::extractFilter($request);
        $output = $this->service->getAll($filtre);
//        dd($output->toArray());
        $tresorerie = $this->tresorerieService->getAll([]);

        return Inertia::render("ModuleTresorerie/TresorerieFlux/Index", [
            "data" => $output,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', []),
            "tresoreries" => $tresorerie
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
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
