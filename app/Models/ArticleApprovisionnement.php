<?php

namespace App\Models;

use App\Traits\HasValueText;
use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class ArticleApprovisionnement extends Basemodel
{
    use HasFactory;
    use HasValueText;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
    public function details()
    {
        return $this->hasMany(ArticleApproDetail::class);
    }

    public function pneuSeries()
    {
        return $this->hasMany(PneuSerie::class, 'article_approvisionnement_id');
    }

    public function bonCommande(): BelongsTo
    {
        return $this->belongsTo(BoncommandeFournisseur::class, 'boncommande_fournisseur_id');
    }


    /**
     * Scope a query to filter results based on search criteria.
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('date_appro', 'LIKE', "%{$search}%")
                ->orWhere('numero_bon_commande', 'LIKE', "%{$search}%")
                ->orWhereHas('fournisseur', function ($q) use ($search) {
                    $q->where('nom_fournisseur', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('magasin', function ($q) use ($search) {
                    $q->where('nom_magasin', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
        });
    }

    public function scopeMagasin($query, $magasin_id)
    {
        if (!is_null($magasin_id)) {
            $query->where('magasin_id', '=', $magasin_id);
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date)
    {

        if (!is_null($date)) {
            $query->where('date_appro', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date)
    {
        if (!is_null($date)) {
            $query->where('date_appro', '>=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function toArray()
    {
        $default = parent::toArray();

        $default['nom_user'] = $this->user?->name;
        $default['nom_magasin'] = $this->magasin?->nom_magasin;
        $default['nom_fournisseur'] = $this->fournisseur?->nom_fournisseur;
        return $default;
    }
}
