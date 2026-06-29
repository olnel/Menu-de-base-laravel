<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paie extends Basemodel
{
    use HasFactory;

    protected $fillable = [
        'salarie_id',
        'mois',
        'annee',
        'salaire_base',
        'montant_primes',
        'montant_retenues',
        'salaire_net',
        'statut',
        'date_paiement',
        'mode_paiement',
        'reference_paiement',
        'telephone_paiement',
        'tresorerie_id',
        'user_id',
    ];

    public function salarie()
    {
        return $this->belongsTo(Salarie::class);
    }

    public function elements()
    {
        return $this->hasMany(PaieElement::class);
    }

    public function tresorerie()
    {
        return $this->belongsTo(Tresorerie::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMois($query, $mois)
    {
        if (!is_null($mois) && $mois !== '') {
            return $query->where('mois', (int)$mois);
        }
        return $query;
    }

    public function scopeAnnee($query, $annee)
    {
        if (!is_null($annee) && $annee !== '') {
            return $query->where('annee', (int)$annee);
        }
        return $query;
    }

    public function scopeStatut($query, $statut)
    {
        if (!is_null($statut) && $statut !== '') {
            return $query->where('statut', $statut);
        }
        return $query;
    }

    public function scopeFilter($query, $search)
    {
        if ($search) {
            $query->whereHas('salarie', function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%$search%")
                  ->orWhere('prenom', 'LIKE', "%$search%")
                  ->orWhere('matricule', 'LIKE', "%$search%");
            });
        }
        return $query;
    }

    /**
     * Recalculate totals based on elements
     */
    public function recalculate()
    {
        $this->montant_primes = $this->elements()->where('type', 'prime')->sum('montant');
        $this->montant_retenues = $this->elements()->where('type', 'retenue')->sum('montant');
        $this->salaire_net = $this->salaire_base + $this->montant_primes - $this->montant_retenues;
        $this->save();
    }
}
