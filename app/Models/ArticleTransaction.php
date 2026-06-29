<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTransaction extends Basemodel
{
    use HasFactory;

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_mouvement', 'LIKE', "%{$search}%")
                    ->orWhereHas('magasin', fn($m) => $m->where('nom_magasin', 'LIKE', "%{$search}%"))
                    ->orWhereHas('vehicule', fn($v) => $v->where('immatriculation', 'LIKE', "%{$search}%"));
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
            $query->where('date_transaction', '>=', \App\Utils\ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_transaction', '<=', \App\Utils\ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }
    public function magasincible()
    {
        return $this->belongsTo(Magasin::class,'magasin_cible');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function details()
    {
        return $this->hasMany(ArticleTransactionDetail::class);
    }


    public function getNomUserAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function getNomMagasinAttribute(): ?string
    {
        return $this->magasin?->nom_magasin;
    }

    public function getNomMagasinCibleAttribute(): ?string
    {
        return $this->magasincible?->nom_magasin;
    }

    public function getImmatriculationAttribute(): ?string
    {
        return $this->vehicule?->immatriculation;
    }

    public function toArray()
    {
        $default = parent::toArray();
        $default['nom_user']          = $this->nom_user;
        $default['nom_magasin']       = $this->nom_magasin;
        $default['nom_magasin_cible'] = $this->nom_magasin_cible;
        $default['immatriculation']   = $this->immatriculation;
        $default['vehicule_id']       = $this->immatriculation;
        return $default;
    }
}
