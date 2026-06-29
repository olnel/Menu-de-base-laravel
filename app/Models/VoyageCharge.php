<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoyageCharge extends Basemodel
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'montant',
        'voyage_id',
        'tresorerie_id',
        'mode_paiement',
        'tresorerie_mouvement_id',
    ];

    protected $appends = ['nom_tresorerie'];

    public function tresorerie()
    {
        return $this->belongsTo(Tresorerie::class);
    }

    public function voyage()
    {
        return $this->belongsTo(Voyage::class);
    }

    public function tresorerieMouvement()
    {
        return $this->belongsTo(TresorerieMouvement::class);
    }

    public function getNomTresorerieAttribute()
    {
        return $this->tresorerie?->nom_tresorerie;
    }

    public function toArray()
    {
        $default = parent::toArray();
        $default['nom_tresorerie'] = $this->tresorerie?->nom_tresorerie;
        return $default;
    }
}
