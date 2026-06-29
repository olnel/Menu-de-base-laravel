<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class Chauffeur extends Basemodel
{

    use HasFactory;
    use LogsActivity;

    public string $logModule = 'chauffeur';

    protected $fillable = [
        'matricule',
        'img',
        'thumb_img',
        'nom',
        'prenom',
        'date_naissance',
        'CIN',
        'telephone',
        'adresse',
        'salarie_id',
        'is_aide_chauffeur',
        'parent_chauffeur_id',
        'vehicule_id'
    ];

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)){
            $query->where(function($q) use ($search) {
                $q->where('matricule', 'LIKE', "%{$search}%")
                    ->orWhere('nom', 'LIKE', "%{$search}%")
                    ->orWhere('prenom', 'LIKE', "%{$search}%")
                    ->orWhere('adresse', 'LIKE', "%{$search}%")
                    ->orWhere('CIN', 'LIKE', "%{$search}%");

            });
        }
        return $query;
    }
    public function vehicules()
    {
        return $this->belongsToMany(Vehicule::class)->withTimestamps();
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function salarie()
    {
        return $this->belongsTo(Salarie::class, 'salarie_id');
    }

    public function parentChauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'parent_chauffeur_id');
    }

    public function aideChauffeurs()
    {
        return $this->hasMany(Chauffeur::class, 'parent_chauffeur_id');
    }

    public function documents()
    {
    return $this->hasMany(DocumentChauffeur::class);
    }
    public function toArray()
    {
        $default = parent::toArray();
        $default['nom_chauffeur'] = $this->chauffeur ? strtoupper($this->chauffeur?->nom) . ' ' . ucfirst($this->chauffeur?->prenom) : null;
        return $default;
    }
}
