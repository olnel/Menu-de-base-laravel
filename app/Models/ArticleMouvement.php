<?php

namespace App\Models;

use App\Traits\HasDefaultOrder;
use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ArticleMouvement extends Basemodel
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

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class, 'magasin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculerEcart()
    {
        if (!is_null($this->stock_theorique))
            $this->ecart_stock = $this->stock_reel - $this->stock_theorique;

    }

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)){
            $query->where(function($q) use ($search) {
                $q->where('date_mvt', 'LIKE', "%{$search}%")
                    ->orWhere('commentaire_mvt', 'LIKE', "%{$search}%")
                    ->orWhere('operation_mvt', 'LIKE', "%{$search}%")
                    ->orWhere('reference_mvt', 'LIKE', "%{$search}%")
                    ->orWhereHas('article', function ($q) use ($search) {
                        $q->where('reference', 'LIKE', "%{$search}%")
                            ->orWhere('designation', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('magasin', function ($q) use ($search) {
                        $q->where('nom_magasin', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('article.famille', function ($q) use ($search) {
                        $q->where('nom_famille_article', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    public function scopeMagasin($query, $magasin_id = null)
    {
        if (!is_null($magasin_id)){
            $query->where(function ($q) use ($magasin_id){
                $q->where('magasin_id', '=', $magasin_id);
            });
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_inventaire', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_inventaire', '>=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFamillearticle($query, $familleId = null)
    {
        if (!is_null($familleId)) {
            $query->whereHas('article', function ($q) use ($familleId) {
                $q->where('article_famille_id', '=', $familleId);
            });
        }

        return $query;
    }

    public function getReferenceAttribute(): ?string
    {
        return $this->article?->reference;
    }

    public function getDesignationAttribute(): ?string
    {
        return $this->article?->designation;
    }

    public function getNomUserAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function getNomFamilleArticleAttribute(): ?string
    {
        return $this->article?->famille?->nom_famille_article;
    }

    public function getNomMagasinAttribute(): ?string
    {
        return $this->magasin?->nom_magasin;
    }

    public function toArray()
    {
        $default = parent::toArray();
        $default['reference']          = $this->reference;
        $default['designation']        = $this->designation;
        $default['nom_user']           = $this->nom_user;
        $default['nom_famille_article'] = $this->nom_famille_article;
        $default['nom_magasin']        = $this->nom_magasin;
        return $default;
    }
}
