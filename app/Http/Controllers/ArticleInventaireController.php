<?php

namespace App\Http\Controllers;

use App\Models\Magasin;
use App\Services\ArticleFamilleService;
use App\Services\ArticleInventaireService;
use App\Services\MagasinService;
use App\Utils\ExtractFiltre;
use App\Utils\ExtractFiltreArticleInventaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleInventaireController extends Controller
{
    private $service, $magasin_service, $articleService, $articleFamilleService;
    public function __construct(ArticleInventaireService $articleInventaireService, MagasinService $magasinService,
                                ArticleFamilleService $articleFamilleService)
    {
        $this->service= $articleInventaireService;
        $this->magasin_service = $magasinService;
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
        return Inertia::render("ArticleInventaire/Index", [
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

        $validated = $request->validate([
            'magasin_id' => 'required|numeric|exists:magasins,id',
            'date_inventaire' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.article_id' => 'required|numeric|exists:articles,id',
            'details.*.stock_reel' => 'required|numeric|min:0',
            'details.*.ecart_stock' => 'required|numeric',
            'details.*.stock_theorique' => 'required|numeric|min:0',
            'details.*.remarque' => 'nullable|string|max:255'
        ]);

        $output = $this->service->save($validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
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
