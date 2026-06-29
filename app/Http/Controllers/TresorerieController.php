<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTresorerieRequest;
use App\Http\Requests\UpdateTresorerieRequest;
use App\Models\Tresorerie;
use App\Services\TresorerieService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TresorerieController extends Controller
{
    private TresorerieService $tresorerieService;
    public function __construct(TresorerieService $tresorerieService)
    {
        $this->tresorerieService = $tresorerieService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->tresorerieService->getAll($filtre);
        return Inertia::render("ModuleTresorerie/Tresorerie/Index", [
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
    public function store(StoreTresorerieRequest $request)
    {

        $output= $this->tresorerieService->saveTresorerie($request->validated());


        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Tresorerie $tresorerie)
    {
        return back()->with([
            'flash' => [
                'data' => $tresorerie
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
    public function update(UpdateTresorerieRequest $request, Tresorerie $tresorerie)
    {

        $validated = $request->validated();
        $output= $this->tresorerieService->updateTresoreie($tresorerie ,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tresorerie $tresorerie)
    {
        try {
            $output = $this->tresorerieService->delete($tresorerie);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
