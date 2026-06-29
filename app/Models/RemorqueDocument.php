<?php

namespace App\Models;

use App\Utils\ForamatDate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RemorqueDocument extends Basemodel
{
    use HasFactory;
    public function remorque()
    {
        return $this->belongsTo(Remorque::class,'remorque_id');

    }

    public function scopeFilter($query, $search)
    {
        // Filtre de recherche générale
        if ( !is_null($search) && !empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('nom_document', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

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

    public function toArray(): array
    {
        $default = parent::toArray();
        $default['fichier_jointe'] = json_decode($this->fichier_jointe) ?? '';
        $default['date_expiration'] = Carbon::parse($this->date_expiration)->format('d-m-Y');

        return $default;
    }
}
