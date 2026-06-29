<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PneuInventaire extends Basemodel
{
    use HasFactory;


    // Scopes de filtre, calqués sur ArticleInventaire
    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('date_inventaire', 'LIKE', "%{$search}%")
                    ->orWhereHas('article', function ($qa) use ($search) {
                        $qa->where('reference', 'LIKE', "%{$search}%")
                            ->orWhere('designation', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('magasin', function ($qm) use ($search) {
                        $qm->where('nom_magasin', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    public function scopeMagasin($query, $magasin_id = null)
    {
        if (!is_null($magasin_id)) {
            $query->where('magasin_id', '=', $magasin_id);
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_inventaire', '>=', \App\Utils\ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_inventaire', '<=', \App\Utils\ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeArticle($query, $article_id = null)
    {
        if (!is_null($article_id)) {
            $query->where('article_id', '=', $article_id);
        }
        return $query;
    }

    public function scopeUser($query, $user_id = null)
    {
        if (!is_null($user_id)) {
            $query->where('user_id', '=', $user_id);
        }
        return $query;
    }
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function pneu_series()
    {
        return $this->hasMany(PneuSerie::class);
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class, 'magasin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function remorque()
    {
        return $this->belongsTo(Remorque::class, 'remorque_id');
    }

    public function toArray()
    {
        $default = parent::toArray();
        $default['reference']      = $this->article?->reference;
        $default['designation']    = $this->article?->designation;
        $default['nom_user']       = $this->user?->name;
        $default['nom_magasin']    = $this->magasin?->nom_magasin ?? $this->magasin?->nom;
        $default['vehicule_label'] = $this->vehicule?->immatriculation;
        $default['remorque_label'] = $this->remorque?->numero_remorque;
        return $default;
    }


}
