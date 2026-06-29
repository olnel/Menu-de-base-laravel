<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreMagasnRequest;
use App\Http\Requests\UpdateMagasnRequest;
use App\Models\Magasin;
use App\Services\MagasinService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MagasinController extends Controller
{
    private $service;

    public function __construct(MagasinService $magasinService)
    {
        $this->service = $magasinService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("Magasin/Index", [
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
    public function store(StoreMagasnRequest $request)
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
    public function show(Magasin $magasin)
    {
        return back()->with([
            'flash' => [
                'data' => $magasin
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
    public function update(UpdateMagasnRequest $request, Magasin $magasin)
    {
        $validated = $request->validated();

        $output= $this->service->update($magasin ,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Magasin $magasin)
    {
        try {
            $output = $this->service->delete($magasin);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
