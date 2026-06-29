<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportRequest;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImportController extends Controller
{
    protected ImportService $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index(Request $request)
    {
        $output = ['chauffeur', 'vehicule', 'remorque', 'client'];

        return Inertia::render("Import/Index", [
            "data" => $output,
        ]);
    }

    public function import(ImportRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->importService->importDynamic($validated);
            return back()->with('message.success', 'Import réussi');
        } catch (\Throwable $e) {
            return back()->with('message.error', $e->getMessage());
            // return back()->withErrors(['import' => $e->getMessage()]);
        }
    }

    public function import_chauffeur_all(ImportRequest $request)
    {
        try {
            $this->importService->importChauffeurVehicule($request->file('file'));
            return back()->with('success', 'Import réussi');
        } catch (\Throwable $e) {
            return back()->with('message.error', $e->getMessage());
        }
    }

    public function import_vehicule(ImportRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->importService->importVehicule($validated);
            return back()->with('success', 'Import réussi');
        } catch (\Throwable $e) {
            return back()->with('message.error', $e->getMessage());
        }
    }

    public function import_chauffeur(ImportRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->importService->importChauffeur($validated);
            return back()->with('success', 'Import réussi');
        } catch (\Throwable $e) {
            return back()->with('message.error', $e->getMessage());
        }
    }
}
