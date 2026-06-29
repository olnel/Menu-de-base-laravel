<?php

namespace App\Models;

use App\Traits\HasValueText;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Vehicule extends Basemodel
{
    use HasFactory;
    use HasValueText;
    use LogsActivity;

    public string $logModule = 'vehicule';

    /**
     * Scope pour filtrer les véhicules selon une recherche
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFilter($query, $search = null)
    {
        if (is_null($search) || $search === '') {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('immatriculation', 'LIKE', "%{$search}%")
                ->orWhere('marque', 'LIKE', "%{$search}%")
                ->orWhere('modele', 'LIKE', "%{$search}%")
                ->orWhere('num_chassis', 'LIKE', "%{$search}%")
                ->orWhere('couleur', 'LIKE', "%{$search}%")
                ->orWhere('num_carte_grise', 'LIKE', "%{$search}%")
                ->orWhere('valeur_initial', 'LIKE', "%{$search}%");
        });
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
        return $this->hasMany(VehiculePhoto::class);
    }

    public function remorque()
    {
        return $this->belongsTo(\App\Models\Remorque::class, 'remorque_id');
    }

    public function element_vehicules()
    {
        return $this->hasMany(VehiculeElement::class, 'vehicule_id');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function planningCalendars()
    {
        return $this->hasMany(PlanningCalendar::class, 'vehicule_id');
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class, 'vehicule_id');
    }

    public function chauffeurs()
    {
        return $this->belongsToMany(Chauffeur::class)->withTimestamps();
    }

    public function pieces()
    {
        return $this->morphMany(VehiculePieces::class, 'proprietaire');
    }

    public function gpsPositions()
    {
        return $this->hasMany(GpsPosition::class);
    }

    public function dernierePosition()
    {
        return $this->hasOne(GpsPosition::class)->latestOfMany('gps_at');
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'numero_remorque' => $this->remorque?->numero_remorque,
        ]);
    }
}
