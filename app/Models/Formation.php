<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formation extends Basemodel
{
    protected $fillable = [
        'nom',
        'description',
        'periode_renouvellement_mois',
        'alerte_avant_jours',
        'formation_suivante_id',
    ];

    protected $casts = [
        'periode_renouvellement_mois' => 'integer',
        'alerte_avant_jours' => 'integer',
    ];

    /**
     * Formation suivante dans le chaînage (A → B → C)
     */
    public function formationSuivante(): BelongsTo
    {
        return $this->belongsTo(self::class, 'formation_suivante_id');
    }

    /**
     * Formation précédente dans le chaînage (si celle-ci est la suivante d'une autre)
     */
    public function formationPrecedente(): HasMany
    {
        return $this->hasMany(self::class, 'formation_suivante_id');
    }

    /**
     * Sessions de cette formation
     */
    public function sessionFormations(): HasMany
    {
        return $this->hasMany(SessionFormation::class);
    }

    /**
     * Dernière session enregistrée
     */
    public function derniereSession(): ?SessionFormation
    {
        return $this->sessionFormations()->latest('date_formation')->first();
    }

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }
}
