<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleTransactionRequest;
use App\Http\Requests\UpdateArticleTransactionRequest;
use App\Models\ArticleTransaction;
use App\Services\ArticleFamilleService;
use App\Services\ArticleTransactionService;
use App\Services\MagasinService;
use App\Services\VehiculeService;
use App\Utils\ExtractFiltreArticleApprovisionnement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleTransactionController extends Controller
{
    private ArticleTransactionService $service;
    private MagasinService $magasinService;
    private ArticleFamilleService $articleFamilleService;
    private VehiculeService $vehiculeService;

    public function __construct(ArticleTransactionService $articleTransactionService, MagasinService $magasinService, ArticleFamilleService $articleFamilleService,
                    VehiculeService $vehiculeService)
    {
        $this->service = $articleTransactionService;
        $this->magasinService = $magasinService;
        $this->articleFamilleService = $articleFamilleService;
        $this->vehiculeService = $vehiculeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreArticleApprovisionnement::extractFilter($request);

        $output = $this->service->getAll($filtre);
        $magasins = $this->magasinService->getAll([]);
        $article_famille = $this->articleFamilleService->getAll([]);
        $vehicules = $this->vehiculeService->getAll([]);

        return Inertia::render("ArticleMouvement/Index", [
            "data" => $output,
            "magasins" => $magasins,
            "vehicules" => $vehicules,
            "famille_articles" => $article_famille,
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
    public function store(StoreArticleTransactionRequest $request)
    {

        $output= $this->service->saveWithDetails($request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ArticleTransaction $article_mouvement)
    {
        $article_mouvement->load([ 'details']);
        return back()->with([
            'flash' => [
                'data' => $article_mouvement
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
    public function update(UpdateArticleTransactionRequest $request, ArticleTransaction $article_mouvement)
    {
        $output= $this->service->editWithDetails( $article_mouvement,$request->validated());

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleTransaction $article_mouvement)
    {
        try {
            $output = $this->service->deleteWithDetails($article_mouvement);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }


    public function generatereference()
    {
        $output = $this->service->generateReference();

        return back()->with([
            'flash' => [
                'data' => $output,
            ]
        ]);
    }
}
