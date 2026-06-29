<?php

namespace App\Http\Controllers;

use App\Models\ArticleFamille;
use App\Services\ArticleFamilleService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;


class ArticleFamilleController extends Controller
{
    private $service;

    public function __construct(ArticleFamilleService $articleFamilleService)
    {
        $this->service = $articleFamilleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $output = $this->service->getAll($filtre);

        return Inertia::render("ArticleFamille/Index", [
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
            'nom_famille_article' => ['required',
                'string',
                'max:255',
                Rule::unique('article_familles')->whereNull('deleted_at')
            ],
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
    public function show(ArticleFamille $article_famille)
    {
        return back()->with([
            'flash' => [
                'data' => $article_famille
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ArticleFamille $article_famille)
    {
        $validated = $request->validate([
            'nom_famille_article' => [
                'required',
                'string',
                'max:255',
                Rule::unique('article_familles')
                    ->ignore($article_famille->id)
                    ->whereNull('deleted_at')
            ],
        ]);

        $output = $this->service->update($article_famille, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleFamille $article_famille)
    {
        try {
            $output = $this->service->deleteFamille($article_famille);
            return back()->with(
                $output['error'] ? 'message.error' : 'message.success',
                $output['message']
            );
        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }
}
