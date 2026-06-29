<?php

namespace App\Models;

//use App\Traits\HasDefaultOrder;
use App\Traits\LogsActivity;
use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use function Laravel\Prompts\search;

class Voyage extends Basemodel
{
    use HasFactory;
    use LogsActivity;
    //    use HasDefaultOrder;

    public string $logModule = 'voyage';

    protected $appends = ['matricule_vehicule'];
    /*protected static string $defaultOrderColumn = 'date_voyage';
    protected static string $defaultOrderDirection = 'desc';

    protected static function boot()
    {
        parent::boot();
        static::bootHasDefaultOrder();
    }*/

    public function scopeFilter($query, $search = null)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('destination', 'LIKE', "%$search%")
                    ->orWhere('type_trajet', 'LIKE', "%$search%")
                    ->orWhere('etat_reception', 'LIKE', "%$search%")
                    ->orWhere('voyages.commentaire', 'LIKE', "%$search%")
                    ->orWhereHas('reservation.client', function ($query) use ($search) {
                        $query->where('nom_client', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('reservation', function ($query) use ($search) {
                        $query->where('numero_reservation', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('vehicule', function ($query) use ($search) {
                        $query->where('immatriculation', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('chauffeur', function ($query) use ($search) {
                        $query->where('nom', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('remorque', function ($query) use ($search) {
                        $query->where('numero_remorque', 'LIKE', "%$search%");
                    });
            });
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date )
    {
        if (!is_null($date)) {
            $query->where('date_voyage', '<=', ForamatDate::normaliserDate($date));

        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date)
    {
        if (!is_null($date)) {
            $query->where('date_voyage', '>=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }
    public function scopeFilterEtatFacture($query, $etat_facture = null)
    {
        if (!is_null($etat_facture)) {
            $query->where('etat_facture', $etat_facture);
        }
        return $query;
    }

    public function scopeFilterClient($query, $client_id)
    {
        if (!is_null($client_id)) {
            $query->whereHas('reservation', function ($query) use ($client_id) {
                $query->where('client_id', $client_id);
            });
        }
        return $query;
    }
    public function scopeFilterVehicule($query, $vehicule_id)
    {
        if (!is_null($vehicule_id)) {
            $query->where('vehicule_id', $vehicule_id);
        }
        return $query;
    }
    public function scopeFilterUser($query, $user_id = null)
    {

        if (!is_null($user_id)) {
            $query->where('user_id', $user_id);
        }
        return $query;
    }
    public function scopeFilterChauffeur($query, $chauffeur_id)
    {

        if (!is_null($chauffeur_id)) {
            $query->where('chauffeur_id', $chauffeur_id);
        }
        return $query;
    }

    public function scopeFilterStatut($query, $statut)
    {
        if (!is_null($statut)) {
            $query->where('statut', $statut);
        }
        return $query;
    }



    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
    public function aideChauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'aide_chauffeur_id');
    }
    public function remorque()
    {
        return $this->belongsTo(Remorque::class);
    }
    public function getMatriculeVehiculeAttribute()
    {
        return $this->vehicule?->immatriculation;
    }

    public function toArray()
    {
        $default = parent::toArray();
        $default['nom_chauffeur'] = $this->chauffeur ? strtoupper($this->chauffeur?->nom) . ' ' . ucfirst($this->chauffeur?->prenom) : null;
        $default['nom_aide_chauffeur'] = $this->aideChauffeur ? strtoupper($this->aideChauffeur?->nom) . ' ' . ucfirst($this->aideChauffeur?->prenom) : null;
        $default['matricule_vehicule'] = $this->vehicule?->marque . ' ' . $this->vehicule?->modele . ' (' . $this->vehicule?->immatriculation . ')';
        $default['numero_remorque'] = $this->remorque?->numero_remorque;
        $default['facture_client_id'] = $this->facture_client_id ?? null;
        return $default;
    }

    public function VoyageMarchandises()
    {
        return $this->hasMany(VoyageMarchandise::class);
    }
    public function VoyageCharges()
    {
        return $this->hasMany(VoyageCharge::class);
    }
    public function carburantTransactions()
    {
        return $this->hasMany(CarburantTransaction::class);
    }
    public function voyageAffectation()
    {
        return $this->hasOne(VoyageAffectation::class);
    }

    public function pneus()
    {
        return $this->belongsToMany(PneuSerie::class, 'voyage_pneus')
            ->withPivot('is_secours', 'numero_serie', 'position', 'designation', 'etat')
            ->withTimestamps();
    }
}
