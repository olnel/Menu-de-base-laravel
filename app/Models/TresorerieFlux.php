<?php

namespace App\Models;

use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TresorerieFlux extends Basemodel
{
    use HasFactory;
    protected $appends = ['nom_user', 'nom_tresorerie', 'nom_tresorerie_cible'];
    public function getNomUserAttribute()
    {
        return $this->user?->name;
    }

    public function getNomTresorerieAttribute()
    {
        return $this->tresorerie?->nom_tresorerie;
    }

    public function getNomTresorerieCibleAttribute()
    {
        return $this->tresoreriecible?->nom_tresorerie;
    }

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)){
            $query->where(function($q) use ($search) {
                $q->where('libelle_mvt', 'LIKE', "%{$search}%")
                    ->orWhere('reference_mvt', 'LIKE', "%{$search}%")
                    ->orWhere('operation_mvt', 'LIKE', "%{$search}%")
                    ->orWhere('mode_paiement', 'LIKE', "%{$search}%")
                    ->orWhere('type_mvt', 'LIKE', "%{$search}%")
                    ->orWhere('tresorerie_fluxes.commentaire', 'LIKE', "%{$search}%")
                    ->orWhereHas('tresorerie', function ($q) use ($search) {
                        $q->where('nom_tresorerie', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('tresoreriecible', function ($q) use ($search) {
                        $q->where('nom_tresorerie', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    public function scopeTresorerie($query, $tresorerie_id = null)
    {
        if (!is_null($tresorerie_id)){
            $query->where(function ($q) use ($tresorerie_id){
                $q->where('tresorerie_id', '=', $tresorerie_id);
            });
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_mvt', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_mvt', '>=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }
    public function tresorerie()
    {
        return $this->belongsTo(Tresorerie::class, 'tresorerie_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function tresoreriecible()
    {
        return $this->belongsTo(Tresorerie::class, 'tresorerie_id_cible');
    }
}
