<?php

namespace App\Models;

use App\Traits\HasValueText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salarie extends Basemodel
{
    use HasFactory;
    use HasValueText;
    use SoftDeletes;

    protected $fillable = [
        'matricule',
        'count_matricule',
        'nom',
        'prenom',
        'sexe',
        'salaire',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'cin',
        'date_cin',
        'lieu_cin',
        'telephone',
        'email',
        'adresse',
        'photo',
        'date_embauche',
        'statut',
        'observation',
        'type_salarie_id',
    ];

    public function typeSalarie()
    {
        return $this->belongsTo(TypeSalarie::class, 'type_salarie_id');
    }

    public function chauffeur()
    {
        return $this->hasOne(Chauffeur::class, 'salarie_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function histories()
    {
        return $this->hasMany(SalarieHistory::class);
    }

    public function scopeOnlyTrashed($query, $onlyTrashed)
    {
        if ($onlyTrashed) {
            return $query->onlyTrashed();
        }
        return $query;
    }

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'LIKE', "%$search%")
                  ->orWhere('nom', 'LIKE', "%$search%")
                  ->orWhere('prenom', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhere('cin', 'LIKE', "%$search%");
            });
        }
        return $query;
    }
}
