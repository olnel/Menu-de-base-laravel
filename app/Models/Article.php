<?php

namespace App\Models;

use App\Traits\HasDefaultOrder;
use App\Traits\HasValueText;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Article extends Basemodel
{
    use HasFactory;
    use HasValueText;
    use HasDefaultOrder;
    use LogsActivity;

    public string $logModule = 'article';
    protected static string $defaultOrderColumn = 'designation';
    protected static string $defaultOrderDirection = 'asc';
    protected static function boot()
    {
        parent::boot();
        static::bootHasDefaultOrder();
    }
    /**
     * Champs calculés automatiquement dans les résultats JSON
     */
    protected $appends = ['total_stock', 'nom_famille_article', 'stock_contextuel'];

    /**
     * Relation avec la famille d'article
     */
    public function famille()
    {
        return $this->belongsTo(ArticleFamille::class, 'article_famille_id');
    }

    /**
     * Relation many-to-many avec les magasins (table pivot article_magasin)
     * Inclut le stock et le timestamp de modification
     */
    public function magasins()
    {
        return $this->belongsToMany(Magasin::class, 'article_magasin')
            ->withPivot(['stock', 'deleted_at'])
            ->withTimestamps();
    }

    /**
     * Calcule le stock total (somme des stocks dans tous les magasins)
     */
    public function getTotalStockAttribute()
    {
        // Charge la relation si elle n'est pas déjà chargée
        if (!$this->relationLoaded('magasins')) {
            $this->load('magasins');
        }

        return $this->magasins->sum('pivot.stock');
    }

    /**
     * Retourne le nom de la famille d'article (accessor)
     */
    public function getNomFamilleArticleAttribute()
    {
        return $this->famille?->nom_famille_article;
    }

    /**
     * Détermine le stock contextuel:
     * - Stock dans le magasin filtré si filtre appliqué
     * - Stock total sinon
     */
    public function getStockContextuelAttribute()
    {
        $request = app(Request::class);
        $magasinId = $request->input('magasin_id');

        // Si filtre magasin et relation chargée
        if ($magasinId && $this->relationLoaded('magasins')) {
            $magasin = $this->magasins->firstWhere('id', $magasinId);
            return $magasin ? $magasin->pivot->stock : 0;
        }

        return $this->total_stock;
    }

    /**
     * Scope pour la recherche globale
     */
    public function scopeFilter($query, $search)
    {
        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'LIKE', "%{$search}%")
                    ->orWhere('designation', 'LIKE', "%{$search}%")
                    ->orWhere('type_article', 'LIKE', "%{$search}%")
                    ->orWhere('marque', 'LIKE', "%{$search}%")
                    ->orWhere('valeur', 'LIKE', "%{$search}%")
                    ->orWhereHas('famille', function ($sq) use ($search) {
                        $sq->where('nom_famille_article', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    /**
     * Scope pour filtrer par type d'article
     */
    public function scopeTypearticle($query, $type = null)
    {
        $type = $type ?? app(Request::class)->type_article;
        return $query->when($type, function ($q) use ($type) {
            // Recherche insensible à la casse et aux accents via LIKE
            $q->where('type_article', 'LIKE', $type);
        });
    }

    public function scopeExcludetypearticle($query, $type = null)
    {
        return $query->when($type, fn($q) => $q->whereRaw('LOWER(type_article) != LOWER(?)', [strtolower($type)]));
    }


    public function inventaires()
    {
        return $this->hasMany(ArticleInventaire::class);
    }

    public function stocks()
    {
        return $this->hasMany(ArticleMagasin::class);
    }

    /**
     * Formatage des résultats
     */
    public function toArray()
    {
        $isPneu = strtolower($this->type_article ?? '') === 'pneu';
        $stock = $this->stock_contextuel;
        $totalStock = $this->total_stock;
        $enUtilisation = null;

        if ($isPneu) {
            $pneuDisponible = array_key_exists('pneu_stock_disponible', $this->attributes)
                ? (int) $this->attributes['pneu_stock_disponible']
                : $this->pneu_series()->whereNull('vehicule_id')->whereNull('remorque_id')->count();

            $pneuTotal = array_key_exists('pneu_stock_total', $this->attributes)
                ? (int) $this->attributes['pneu_stock_total']
                : $this->pneu_series()->count();

            $stock = $pneuDisponible;
            $totalStock = $pneuTotal;
            $enUtilisation = $pneuTotal - $pneuDisponible;
        }

        return array_merge(parent::toArray(), [
            'stocks' => $this->magasins->map(function ($magasin) {
                return [
                    'magasin_id' => $magasin->id,
                    'nom_magasin' => $magasin->nom,
                    'stock' => $magasin->pivot->stock,
                    'updated_at' => $magasin->pivot->updated_at
                ];
            }),
            'total_stock' => $totalStock,
            'stock' => $stock,
            'pneu_en_utilisation' => $enUtilisation,
        ]);
    }
    public function pneu_series()
    {
        return $this->hasMany(PneuSerie::class);
    }
}
