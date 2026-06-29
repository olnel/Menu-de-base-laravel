<?php

namespace App\Http\Controllers;

use App\Models\TypeSalarie;
use App\Services\TypeSalarieService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TypeSalarieController extends Controller
{
    private $service;

    public function __construct(TypeSalarieService $typeSalarieService)
    {
        $this->service = $typeSalarieService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("TypeSalarie/Index", [
            "data" => $output,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', [])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('type_salaries')->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
        ]);

        $output = $this->service->create($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeSalarie $type_salarie)
    {
        return back()->with([
            'flash' => [
                'data' => $type_salarie
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeSalarie $type_salarie)
    {
        $validated = $request->validate([
            'libelle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('type_salaries')
                    ->ignore($type_salarie->id)
                    ->whereNull('deleted_at')
            ],
            'description' => 'nullable|string',
        ]);

        $output = $this->service->update($type_salarie, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeSalarie $type_salarie)
    {
        try {
            $output = $this->service->delete($type_salarie);
            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );
        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
