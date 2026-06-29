<?php

namespace App\Http\Controllers;

use App\Services\ArticleFamilleService;
use App\Services\ArticleInventaireService;
use App\Services\ArticleMouvementService;
use App\Services\ArticleService;
use App\Services\MagasinService;
use App\Utils\ExtractFiltreArticleInventaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleMouvementController extends Controller
{
    private $service, $magasin_service, $articleService, $articleFamilleService;
    public function __construct(ArticleMouvementService $articleMouvementService, MagasinService $magasinService, ArticleService $articleService,
                                ArticleFamilleService $articleFamilleService)
    {
        $this->service= $articleMouvementService;
        $this->magasin_service = $magasinService;
        $this->articleService = $articleService;
        $this->articleFamilleService = $articleFamilleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreArticleInventaire::extractFilter($request);
        $output = $this->service->getAll($filtre);
        $magasins = $this->magasin_service->getAll([]);
        $article_famille = $this->articleFamilleService->getAll([]);
        return Inertia::render("ArticleFlux/Index", [
            "data" => $output,
            "magasins" => $magasins,
            "famille_articles" =>$article_famille,
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
