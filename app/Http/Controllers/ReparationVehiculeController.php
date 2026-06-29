<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReparationVehiculeRequest;
use App\Http\Requests\UpdateReparationVehiculeRequest;
use App\Models\ReparationVehicule;
use App\Models\Vehicule;
use App\Models\Magasin;
use App\Models\Article;
use App\Models\Remorque;
use App\Models\Salarie;
use App\Services\ReparationVehiculeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReparationVehiculeController extends Controller
{
    protected $service;

    public function __construct(ReparationVehiculeService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ReparationVehicule::with([
            "articles.details.magasin",
            "vehicule",
            "user",
            "responsable",
        ]);

        if ($request->search) {
            $query
                ->where("immatriculation", "like", "%" . $request->search . "%")
                ->orWhere("observations", "like", "%" . $request->search . "%");
        }

        // Filter par date de réparation
        if ($request->date_reparation) {
            $query->whereDate("date_reparation", $request->date_reparation);
        }

        // Filter par véhicule
        if ($request->vehicule_id) {
            $query->where("vehicule_id", $request->vehicule_id);
        }

        // Filter par remorque (via les articles liés)
        if ($request->numero_remorque) {
            $query->whereHas("articles", function ($q) use ($request) {
                $q->where("is_remorque", true)
                    ->where("numero_remorque", $request->numero_remorque);
            });
        }

        $data = $query->latest()->paginate(10)->withQueryString();

        $vehicules = Vehicule::all(["id", "immatriculation"]);
        foreach ($vehicules as $vehicule) {
            $vehicule->value = $vehicule->id;
            $vehicule->label = $vehicule->immatriculation;
        }

        $magasins = Magasin::all(["id", "nom_magasin as label"]);
        foreach ($magasins as $magasin) {
            $magasin->value = $magasin->id;
        }

        $remorques = Remorque::all(["id", "numero_remorque"]);
        foreach ($remorques as $remorque) {
            $remorque->value = $remorque->id;
            $remorque->label = $remorque->numero_remorque;
        }

        $salaries = Salarie::all(["id", "nom", "prenom"]);

        // Articles for "Article à Remplacer" (all articles)
        $articles = Article::where("type_article", "!=", "Pneu")
            ->where("type_article", "!=", "CONSOMMABLE")
            ->get(["id", "reference", "designation", "type_article"]);

        return Inertia::render("ReparationVehicule/Index", [
            "data" => $data,
            "vehicules" => $vehicules,
            "magasins" => $magasins,
            "remorques" => $remorques,
            "salaries" => $salaries,
            "articles" => $articles,
            "filters" => $request->only(["search", "date_reparation", "vehicule_id", "numero_remorque"]),
        ]);
    }

    /**
     * Get articles filtered by magasin with stock info
     */
    public function getArticlesByMagasin(Request $request)
    {
        $magasinId = $request->magasin_id;
        $typeArticle = $request->type_article;

        $query = Article::query();
        if ($typeArticle) {
            $query->where("type_article", $typeArticle);
        } else {
            $query
                ->where("type_article", "!=", "Pneu")
                ->where("type_article", "!=", "CONSOMMABLE");
        }
        $articles = $query
            ->whereHas("magasins", function ($q) use ($magasinId) {
                $q->where("magasin_id", $magasinId);
            })
            ->with([
                "magasins" => function ($q) use ($magasinId) {
                    $q->where("magasin_id", $magasinId);
                },
            ])
            ->get()
            ->map(function ($article) {
                $magasin = $article->magasins->first();
                return [
                    "id" => $article->id,
                    "reference" => $article->reference,
                    "designation" => $article->designation,
                    "stock" => $magasin ? $magasin->pivot->stock : 0,
                    "label" =>
                        $article->designation .
                        " (" .
                        $article->reference .
                        ") - Stock: " .
                        ($magasin ? $magasin->pivot->stock : 0),
                    "value" => $article->id,
                    "prix_unitaire" => $article->valeur,
                ];
            });

        return response()->json($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReparationVehiculeRequest $request)
    {
        // dd($request);
        
        $output = $this->service->save(
            $request->all(),
            new ReparationVehicule(),
        );

        return back()->with(
            $output["error"] ? "message.error" : "message.success",
            $output["message"],
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(ReparationVehicule $reparation_vehicule)
    {
        $reparation_vehicule->load([
            "articles.details.article.magasins",
            "responsable",
        ]);

        // Transform data to include stock_disponible for each detail
        $reparation_vehicule->articles->each(function ($article) {
            $article->details->each(function ($detail) {
                if ($detail->article_id && $detail->magasin_id) {
                    $art = $detail->article;
                    $magasin = $art->magasins->firstWhere(
                        "id",
                        $detail->magasin_id,
                    );
                    $detail->stock_disponible = $magasin
                        ? $magasin->pivot->stock
                        : 0;
                } else {
                    $detail->stock_disponible = 0;
                }
            });
        });

        return back()->with("data", $reparation_vehicule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateReparationVehiculeRequest $request,
        ReparationVehicule $reparation_vehicule,
    ) {
        $output = $this->service->save($request->all(), $reparation_vehicule);

        return back()->with(
            $output["error"] ? "message.error" : "message.success",
            $output["message"],
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReparationVehicule $reparation_vehicule)
    {
        $output = $this->service->deleteReparation($reparation_vehicule);

        return back()->with(
            $output["error"] ? "message.error" : "message.success",
            $output["message"],
        );
    }
}
