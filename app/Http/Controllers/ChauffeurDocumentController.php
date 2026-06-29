<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\DocumentChauffeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Services\ChauffeurDocumentService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChauffeurDocumentController extends Controller
{
    protected $service;

    public function __construct(ChauffeurDocumentService $chauffeurDocumentService)
    {
        $this->service = $chauffeurDocumentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Implement index method
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'nom_document' => 'required|string',
            'date_expiration' => 'nullable|date', // Should handle 'YYYY-MM-DD' format from frontend
            'description' => 'nullable|string',
            'fichier_jointe' => 'nullable|array', // Correct validation field name
            'chauffeur_id' => 'required|exists:chauffeurs,id'
        ]);
//        dd($validated);
        // dd($validated);
        $output = $this->service->create($validated);
        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $documentId)
    {


        $validated = $request->validate([
            'nom_document' => 'required|string',
            'date_expiration' => 'nullable|date',
            'description' => 'nullable|string',
            'fichier_jointe' => 'nullable|array',
        'fichier_jointe.*' => 'file',
            'existing_files' => 'nullable|string',
            'chauffeur_id' => 'required|exists:chauffeurs,id'
        ]);

        $output = $this->service->update($documentId, $validated);
        // Similar to store, back() will close the modal. Consider returning JSON instead.
        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $documentId)
    {
        try {
            $output = $this->service->delete($documentId);
            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );
        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $documentId)
    {
        $document = DocumentChauffeur::findOrFail($documentId);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
}
