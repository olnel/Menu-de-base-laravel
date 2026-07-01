<?php

namespace App\Http\Controllers;

use App\Models\Paie;
use App\Models\PaieElement;
use App\Services\PaieService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaieController extends Controller
{
    private $service;

    public function __construct(PaieService $paieService)
    {
        $this->service = $paieService;
    }

    public function index(Request $request)
    {
        $filters = ExtractFiltre::extractFilter($request);
        
        $filters['mois'] = $request->input('mois', now()->month);
        $filters['annee'] = $request->input('annee', now()->year);
        $filters['statut'] = $request->input('statut');
        $filters['search'] = $request->input('search');

        // Test direct sans passer par le repository si possible ou forcer les paramètres
        $data = $this->service->getAll($filters);
        
        return Inertia::render("Paie/Index", [
            "data" => $data,
            "filters" => $filters,
            "flash" => session('flash', [])
        ]);
    }

    /**
     * Récupère les données pour la prévisualisation de la génération
     */
    public function preview(Request $request)
    {
        $request->validate([
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2020',
        ]);

        $output = $this->service->getGenerationPreview($request->mois, $request->annee);

        return response()->json($output);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'mois' => 'required|integer|between:1,12',
            'annee' => 'required|integer|min:2020',
            'data' => 'required|array',
        ]);

        $output = $this->service->bulkGenerate($request->mois, $request->annee, $request->data);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function storeElement(Request $request, Paie $paie)
    {
        $request->validate([
            'elements' => 'required|array|min:1',
            'elements.*.type' => 'required|in:prime,retenue',
            'elements.*.libelle' => 'required|string|max:255',
            'elements.*.montant' => 'required|numeric|min:0',
        ]);

        try {
            \DB::beginTransaction();
            foreach ($request->elements as $el) {
                $paie->elements()->create($el);
            }
            \DB::commit();
            return back()->with('message.success', 'Éléments enregistrés avec succès');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('message.error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function destroyElement(PaieElement $element)
    {
        $paie = $element->paie;
        $element->delete();
        
        return back()->with('message.success', 'Élément supprimé');
    }

    public function pay(Request $request, Paie $paie)
    {
        $validated = $request->validate([
            'mode_paiement' => 'required|string',
            'date_paiement' => 'nullable|date',
            'reference_paiement' => 'nullable|required_if:mode_paiement,Virement,Chèque,Airtel Money,MVola,Orange Money|string|max:255',
            'telephone_paiement' => 'nullable|required_if:mode_paiement,Airtel Money,MVola,Orange Money|string|max:20',
        ]);

        $output = $this->service->processPayment($paie, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    public function update(Request $request, Paie $paie)
    {
        $validated = $request->validate([
            'salaire_base' => 'required|numeric|min:0',
        ]);

        $paie->update($validated);
        $paie->recalculate();

        return back()->with('message.success', 'Fiche de paie mise à jour');
    }

    /**
     * Impression du tableau récapitulatif des paies (Rapport)
     */
    public function printList(Request $request)
    {
        $filters = ExtractFiltre::extractFilter($request);
        $filters['mois'] = $request->input('mois', now()->month);
        $filters['annee'] = $request->input('annee', now()->year);
        $filters['statut'] = $request->input('statut');
        $filters['search'] = $request->input('search');

        try {
            return $this->service->generateListReportPdf($filters);
        } catch (\Exception $e) {
            \Log::error("Erreur Rapport Liste Paie: " . $e->getMessage());
            return response()->json(['message' => 'Échec de génération du rapport'], 500);
        }
    }

    public function destroy(Paie $paie)
    {
        $paie->delete();
        return back()->with('message.success', 'Fiche de paie supprimée');
    }

    public function print(Paie $paie)
    {
        try {
            return $this->service->generatePdf($paie);
        } catch (\Exception $e) {
            \Log::error("Erreur Impression Paie: " . $e->getMessage(), [
                'paie_id' => $paie->id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Échec de génération du PDF',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Impression en masse des bulletins de paie
     */
    public function massPrint(Request $request)
    {
        $filters = ExtractFiltre::extractFilter($request);
        $filters['mois'] = $request->input('mois', now()->month);
        $filters['annee'] = $request->input('annee', now()->year);
        $filters['statut'] = $request->input('statut');
        $filters['search'] = $request->input('search');

        try {
            return $this->service->generateMassPdf($filters);
        } catch (\Exception $e) {
            \Log::error("Erreur Impression Masse Paie: " . $e->getMessage());
            return response()->json(['message' => 'Échec de génération du PDF'], 500);
        }
    }
}
