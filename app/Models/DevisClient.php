<?php

namespace App\Models;

use App\Traits\LogsActivity;
use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class DevisClient extends Basemodel
{
    use HasFactory;
    use LogsActivity;

    public string $logModule = 'devis_client';

    /**
     * The table associated with the model.
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function($q) use ($search) {
                $q->where('condition_paiement', 'LIKE', "%{$search}%")
                    ->orWhere('condition_delais', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('nom_client', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    /**
     * Filter by end date
     * @param $query
     * @return mixed
     */
    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_devis', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    /**
     * Filter by start date
     * @param $query
     * @return mixed
     */
    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_devis', '>=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    /**
     * Filter by client name
     * @param $query
     * @return mixed
     */
    public function scopeFilterClient($query, $nom_client = null)
    {
        if (!is_null($nom_client)) {
            $query->whereHas('client', function ($q) use ($nom_client) {
                $q->where('nom_client', '=', $nom_client);
            });
        }
        return $query;
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(DevisClientDetails::class);
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'nom_user' => $this->user?->name,
            'nom_client' => $this->client?->nom_client,
        ]);
    }
}
