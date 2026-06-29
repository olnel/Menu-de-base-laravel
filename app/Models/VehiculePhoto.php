<?php

namespace App\Models;

use App\Utils\ForamatDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class VehiculePhoto extends Basemodel
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('orderByDate', function (Builder $builder){
            $builder->orderBy('date_prise_photo', 'asc');
        });
    }


    public function vehicule()
    {
        return $this->hasMany(Vehicule::class);
    }

    public function scopeFilter($query, $search = null)
    {
        // Filtre de recherche générale
        if ( !is_null($search)) {
            $query->where(function($q) use ($search) {
                $q->where('type_element', 'LIKE', "%{$search}%")
                    ->orWhere('etat_vehicule', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    public function scopeTypeelement($query, $type_element = null)
    {
        if (!is_null($type_element)) {
            $query->where('type_element', $type_element);
        }

        return $query;
    }

    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_prise_photo', '>=', ForamatDate::normaliserDate($date));
        }


        return $query;
    }
    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_prise_photo', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }



    public function toArray(): array
    {
        $default = parent::toArray();
        $default['date_prise_photo'] = Carbon::parse($this->date_prise_photo)->format('d-m-Y');
        return $default;
    }
}
