<?php

namespace App\Models;

use App\Utils\ForamatDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class VehiculeDocument extends Basemodel
{
    protected $appends = ['immatriculation'];

    use HasFactory;
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class,'vehicule_id');
    }

    public function scopeFilter($query, $search = null)
    {
        // Filtre de recherche générale
        if (!is_null($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nom_document', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // dd($query);
        return $query;
    }



    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_expiration', '>=', ForamatDate::normaliserDate($date));
        }

        return $query;
    }
    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_expiration', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterVehicule($query, $vehicule_id = null)
    {
        if (!is_null($vehicule_id)) {
            $query->where('vehicule_id', '=', $vehicule_id);
        }
        return $query;
    }

    public function toArray(): array
    {
        $default = parent::toArray();
        $default['fichier_jointe'] = json_decode($this->fichier_jointe) ?? '';
        $default['date_expiration'] = Carbon::parse($this->date_expiration)->format('d-m-Y');
        // $default['immatriculation'] = $this->vehicule?->immatriculation;

        return $default;
    }

    public function getImmatriculationAttribute(){
        return  $this->vehicule?->immatriculation;
    }
}
