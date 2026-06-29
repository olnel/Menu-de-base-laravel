<?php

namespace App\Models;

use App\Traits\HasDefaultOrder;
use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

class BoncommandeFournisseur extends Basemodel
{
    use HasFactory;

    use HasDefaultOrder;
    protected static string $defaultOrderColumn = 'id';
    protected static string $defaultOrderDirection = 'desc';
    protected static function boot()
    {
        parent::boot();
        static::bootHasDefaultOrder();
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function approvisionnement(): HasOne
    {
        return $this->hasOne(ArticleApprovisionnement::class,'boncommande_fournisseur_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BoncommandeFournisseurDetail::class);
    }


    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)){
            $query->where(function($q) use ($search) {
                $q->where('numero_bon_commande', 'LIKE', "%{$search}%")
                    ->orWhereHas('fournisseur', function ($q) use ($search) {
                        $q->where('nom_fournisseur', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_boncommande', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date = null)
    {

        if (!is_null($date)) {
            $query->where('date_boncommande', '>=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }
    public function scopeFilterFournisseur($query, $nom_fournisseur = null)
    {
        if (!is_null($nom_fournisseur)) {
            $query->whereHas('fournisseur', function ($q) use ($nom_fournisseur) {
                $q->where('nom_fournisseur', '=', $nom_fournisseur);
            });
        }
        return $query;
    }

    public function scopeStatut($query, $statut = null)
    {
        if (!is_null($statut)) {
            $statut = (int)$statut;
            if ($statut === 1) {
                return $query->has('approvisionnement');
            }
            elseif ($statut === 0) {
                return $query->doesntHave('approvisionnement');
            }
        }

        return $query;
    }

    public function getNumeroBonLivraisonttribute(): ?string
    {
        return $this->approvisionnement->numero_bon_livraison ?? null;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'nom_user' => $this->user?->name,
            'nom_fournisseur' => $this->fournisseur?->nom_fournisseur,
            'is_genere_appro' => $this->approvisionnement()->exists(),
            'numero_bon_livraison' => $this->getNumeroBonLivraisonttribute(),
        ]);
    }
}
