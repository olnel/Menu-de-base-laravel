<?php

namespace App\Models;

use App\Traits\HasValueText;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Remorque extends Basemodel
{
    use HasFactory;
    use HasValueText;

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search) && $search !== '') {
            $query->where('numero_remorque', 'LIKE', "%{$search}%")
                ->orWhere('modele_remorque', 'LIKE', "%{$search}%")
                ->orWhere('marque_remorque', 'LIKE', "%{$search}%");
        }
        return $query;
    }

    /**
     * Scope pour filtrer par date de début
     */
    public function scopeFilterdatestart($query, $startDate = null)
    {
        if (!is_null($startDate) && $startDate !== '') {
            $query->whereHas('voyages', function ($q) use ($startDate) {
                $q->where('date_voyage', '>=', $startDate);
            });
        }
        return $query;
    }

    /**
     * Scope pour filtrer par date de fin
     */
    public function scopeFilterdateend($query, $endDate = null)
    {
        if (!is_null($endDate) && $endDate !== '') {
            $query->whereHas('voyages', function ($q) use ($endDate) {
                $q->where('date_voyage', '<=', $endDate);
            });
        }
        return $query;
    }

    /**
     * Scope pour filtrer par chauffeur
     */
    public function scopeFilterchauffeur($query, $chauffeurId = null)
    {
        if (!is_null($chauffeurId) && $chauffeurId !== '') {
            $query->whereHas('voyages', function ($q) use ($chauffeurId) {
                $q->where('chauffeur_id', $chauffeurId);
            });
        }
        return $query;
    }

    /**
     * Scope pour filtrer par client
     */
    public function scopeFilterclient($query, $clientId = null)
    {
        if (!is_null($clientId) && $clientId !== '') {
            $query->whereHas('voyages', function ($q) use ($clientId) {
                $q->whereHas('reservation', function ($subQ) use ($clientId) {
                    $subQ->where('client_id', $clientId);
                });
            });
        }
        return $query;
    }

    public function photos()
    {
        return $this->hasMany(RemorquePhoto::class);
    }

    public function element_remorques()
    {
        return $this->hasMany(RemorqueElement::class, 'remorque_id');
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class, 'remorque_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function pieces()
    {
        return $this->morphMany(VehiculePieces::class, 'proprietaire');
    }
}
