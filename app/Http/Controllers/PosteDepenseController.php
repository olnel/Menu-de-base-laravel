<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosteDepenseRequest;
use App\Http\Requests\UpdatePosteDepenseRequest;
use App\Models\PosteDepense;
use App\Services\PosteDepenseService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PosteDepenseController extends Controller
{
    public function __construct(private readonly PosteDepenseService $service)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("PosteDepense/Index", [
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
    public function store(StorePosteDepenseRequest $request)
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
    public function show(PosteDepense $postedepense)
    {
        return back()->with([
            'flash' => [
                'data' => $postedepense
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
    public function update(UpdatePosteDepenseRequest $request, PosteDepense $postedepense)
    {
        $validated = $request->validated();

        $output= $this->service->update($postedepense ,$validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosteDepense $postedepense)
    {
        try {
            $output = $this->service->delete($postedepense);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
