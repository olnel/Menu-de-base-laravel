<?php

namespace App\Models;

use App\Traits\HasNotifications;
use App\Traits\LogsActivity;
use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
class Reservation extends Basemodel
{
    use HasFactory;
    use HasNotifications;
    use LogsActivity;

    public string $logModule = 'reservation';
    public function scopeFilter($query, $search)
    {
        if (!is_null($search)) {
            $query->where('numero_reservation', 'LIKE', "%$search%")
                ->orWhere('lieu_chargement', 'LIKE', "%$search%")
                ->orWhere('lieu_livraison', 'LIKE', "%$search%")
                ->orWhereHas('client', function ($query) use ($search) {
                    $query->where('nom_client', 'LIKE', "%$search%");
                });
        }
//        if (isset($request->client_id)) {
//            $query->where('client_id', $request->client_id);
//        }
        return $query;
    }

    public function scopeFilterdateend($query, $end_date)
    {
        if (!is_null($end_date)) {
            $query->where('date_reservation', '<=', ForamatDate::normaliserDate($end_date));
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $start_date)
    {

        if (!is_null($start_date)) {
            $query->where('date_reservation', '>=', ForamatDate::normaliserDate($start_date));
        }
        return $query;
    }
    public function scopeFilterClient($query, $client_id)
    {
        if (!is_null($client_id)) {
            $query->where('client_id', $client_id);
        }
        return $query;
    }

    public function scopeFilterEtat($query, $etat)
    {
        if (!is_null($etat)) {
            $query->where('etat_facture', $etat);
        }
        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class);
    }
    public function factureClient()
    {
        return $this->belongsTo(FactureClient::class);
    }

    public function toArray()
    {
        $default = parent::toArray();
        $default['nom_client'] = $this->client?->nom_client;
        $default['nom_user'] = $this->user?->name;
        $default['is_factured'] = $this->factureClient?->id ? true : false;

        return $default;
    }

}
