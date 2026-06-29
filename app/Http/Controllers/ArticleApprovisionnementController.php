<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleApproRequest;
use App\Http\Requests\StoreArticleApprovisionnement;
use App\Http\Requests\UpdateArticleApprovisionnementRequest;
use App\Models\ArticleApprovisionnement;
use App\Services\ArticleApprovisionnementService;
use App\Services\ArticleFamilleService;
use App\Services\ArticleService;
use App\Services\FournisseurService;
use App\Services\MagasinService;
use App\Utils\ExtractFiltreArticleApprovisionnement;
use App\Utils\ExtractFiltreArticleInventaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleApprovisionnementController extends Controller
{
    private $service, $magasin_service, $articleFamilleService, $fournisseur_service;

    public function __construct(
        ArticleApprovisionnementService $approvisionnementService,
        MagasinService $magasinService,
        ArticleFamilleService $articleFamilleService,
        FournisseurService $fournisseurService
    ) {
        $this->service = $approvisionnementService;
        $this->magasin_service = $magasinService;
        $this->articleFamilleService = $articleFamilleService;
        $this->fournisseur_service = $fournisseurService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreArticleApprovisionnement::extractFilter($request);

        $output = $this->service->getAll($filtre);
        $magasins = $this->magasin_service->getAll([]);
        $article_famille = $this->articleFamilleService->getAll([]);
        $fournisseurs = $this->fournisseur_service->getAll([]);

        return Inertia::render("ArticleApprovisionnement/Index", [
            "data" => $output,
            "magasins" => $magasins,
            "famille_articles" => $article_famille,
            'fournisseurs' => $fournisseurs,
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
    public function store(StoreArticleApprovisionnement $request)
    {
        $output = $this->service->save($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ArticleApprovisionnement $article_approvisionnement)
    {
        $data = $this->service->formatSeriePneuEdition($article_approvisionnement);
        return back()->with([
            'flash' => [
                'data' => $data
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
    public function update(UpdateArticleApprovisionnementRequest $request, ArticleApprovisionnement $article_approvisionnement)
    {
        $output = $this->service->edit($article_approvisionnement, $request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleApprovisionnement $article_approvisionnement)
    {
        try {
            $output = $this->service->delete($article_approvisionnement);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
