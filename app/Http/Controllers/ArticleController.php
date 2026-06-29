<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticle;
use App\Http\Requests\UpdatearticleRequest;
use App\Models\Article;
use App\Models\Magasin;
use App\Services\ArticleFamilleService;
use App\Services\ArticleService;
use App\Services\MagasinService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ArticleController extends Controller
{
    private $service, $articleFamilleService, $magasin_service;
    public function __construct(ArticleService $articleService, ArticleFamilleService $articleFamilleService, MagasinService $magasinService)
    {
        $this->service = $articleService;
        $this->articleFamilleService = $articleFamilleService;
        $this->magasin_service = $magasinService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltreArticle::extractFilter($request);
        $output = $this->service->fetchData($filtre);
        $article_famille = $this->articleFamilleService->getAll([]);
        $modified_data = $article_famille->map(function ($item) {
            return [
                'label' => $item['label'],
                'value' => $item['label']
            ];
        });
        $magasins = $this->magasin_service->getAll([]);


        return Inertia::render("Article/Index", [
            "data" => $output,
            'magasins' => $magasins,
            "famille_articles" => $article_famille,
            'famille_articles_modified' => $modified_data,
            "filters" => [
                "search" => $request->search
            ],
            "flash" => session('flash', [])
        ]);
    }

    public function getArticle(Request $request)
    {
        $filtre = ExtractFiltreArticle::extractFilter($request);
        $output = $this->service->fetchData($filtre);
        return back()->with([
            'flash' => [
                'data' => $output->toArray(),
            ]
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
    public function store(StoreArticle $request)
    {
        $data = $request->validated();
        $output = $this->service->create($data);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );

    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return back()->with([
            'flash' => [
                'data' => $article
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
    public function update(UpdatearticleRequest $request, Article $article)
    {
        $data = $request->validated();
        $output = $this->service->update($article, $data);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        try {
            $output = $this->service->delete($article);

            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );

        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }


    public function fetchByMagasin(Magasin $magasin, Request $request)
    {

        $filtre = ['magasin_id' => $magasin->id];
        $article = $this->service->fetchData($filtre);

        return back()->with([
            'flash' => [
                'data' => $article->toArray()
            ]
        ]);
    }

    /**
     * Récupère les séries de pneus d'un article (JSON si appelé via fetch)
     */
    public function getPneuSeries(Article $article, \Illuminate\Http\Request $request)
    {
        $result = $this->service->getPneuSeries($article);

        if ($result['error']) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $result['message']], 422);
            }
            return back()->with('message.error', $result['message']);
        }

        if ($request->wantsJson()) {
            return response()->json($result['data']);
        }

        return back()->with(['flash' => ['pneuSeriesData' => $result['data']]]);
    }
}
