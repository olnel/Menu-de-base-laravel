<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoncommandeRequest;
use App\Http\Requests\UpdateBoncommandeRequest;
use App\Models\BoncommandeFournisseur;
use App\Services\ArticleFamilleService;
use App\Services\ArticleService;
use App\Services\BoncommandeService;
use App\Services\FournisseurService;
use App\Services\MagasinService;
use App\Services\PDFService\PDFService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreArticleApprovisionnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BoncommandeController extends Controller
{
    private $service, $magasin_service, $articleFamilleService, $fournisseur_service, $article_service;
    private $pdfService;
    public function __construct(BoncommandeService $boncommandeService, MagasinService $magasinService,
                                ArticleFamilleService $articleFamilleService, FournisseurService $fournisseurService, ArticleService $articleService, PDFService $PDFService)
    {
        $this->service = $boncommandeService;
        $this->magasin_service = $magasinService;
        $this->articleFamilleService = $articleFamilleService;
        $this->fournisseur_service = $fournisseurService;
        $this->article_service = $articleService;
        $this->pdfService = $PDFService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreArticleApprovisionnement::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $article_famille = $this->articleFamilleService->getAll([]);
        $fournisseurs = $this->fournisseur_service->getAll([]);
        $magasins = $this->magasin_service->getAll([]);
        return Inertia::render("ArticleBonCommande/Index", [
            "data" => $output,
            "famille_articles" => $article_famille,
            'fournisseurs' => $fournisseurs,
            "magasins" => $magasins,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', [])
        ]);
    }

    public function generatenumero()
    {
        $output = $this->service->generateNumeroCommande();
        return back()->with([
            'flash' => [
                'data' => $output,
            ]
        ]);
    }


    public function print(BoncommandeFournisseur $article_boncommande)
    {
        try {
            return $this->service->generateBonCommandePdf($article_boncommande);
        } catch (\Exception $e) {
            abort(500, "Échec de génération du PDF : " . $e->getMessage());
        }
    }

    public function sendMail(BoncommandeFournisseur $article_boncommande)
    {

        try {
            $this->service->sendMail($article_boncommande);
            return back()->with('message.success', 'Email envoyé avec succès');
        } catch (\Exception $e) {
            return back()->with('message.error', "Échec de l'envoi : " . $e->getMessage());
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
     * Store a newly created resource in storage.
     */
    public function store(StoreBoncommandeRequest $request)
    {
        $output= $this->service->saveBonCommandeWithDetails($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(BoncommandeFournisseur $article_boncommande)
    {
        $article_boncommande->load([ 'details']);
        return back()->with([
            'flash' => [
                'data' => $article_boncommande
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
    public function update(UpdateBoncommandeRequest $request, BoncommandeFournisseur $article_boncommande)
    {
        $output = $this->service->updateBonCommandeWithDetails($article_boncommande, $request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoncommandeFournisseur $article_boncommande)
    {
        try {
            $output = $this->service->deleteBonCommande($article_boncommande);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
