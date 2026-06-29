<?php

namespace App\Models;

use App\Traits\HasDefaultOrder;
use App\Traits\LogsActivity;
use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FactureClient extends Basemodel
{
    use HasFactory;
    use LogsActivity;
//    use HasDefaultOrder;

    public string $logModule = 'facture_client';

//    protected static string $defaultOrderColumn = 'date_facture';
//    protected static string $defaultOrderDirection = 'desc';
    protected $appends = ['nom_client', 'voyages', 'nom_user'];

 /*   protected static function boot()
    {
        parent::boot();
        static::bootHasDefaultOrder();
    }*/

    public function scopeFilter($query , $search)
    {

        if ($search) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('numero_facture', 'LIKE', "%{$search}%")
                ->orWhere('mode_paiement', 'LIKE', "%{$search}%")
                ->orWhere('statut_facture', 'LIKE', "%{$search}%")
                ->orWhereHas('client', function ($q) use ($search) {
                    $q->where('nom_client', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere(function($subQuery) use ($search) {
                    $voyages = Voyage::where('numero_voyage', 'LIKE', "%{$search}%")->get();

                    if ($voyages->isNotEmpty()) {
                        foreach ($voyages as $voyage) {
                            // Solution robuste avec vérification JSON
                            $subQuery->orWhere(function($q) use ($voyage) {
                                $q->whereRaw('JSON_CONTAINS(voyage_ids, CAST(? AS JSON))', [$voyage->id])
                                    ->orWhere('voyage_ids', 'LIKE', '%"'.$voyage->id.'"%')
                                    ->orWhere('voyage_ids', 'LIKE', '%'.$voyage->id.'%');
                            });
                        }
                    }
                });
        });
    }

    public function scopeFilterclient($query, $client_id)
    {
        $result = $query->where(function ($q) use ($client_id){
            $q->where('client_id', '=', $client_id);
        });
        return $result;
    }

    public function scopeFilterstatut($query, $statut_facture)
    {
        $result = $query->where(function ($q) use ($statut_facture){
            $q->where('statut_facture', '=', $statut_facture);
        });
        return $result;
    }


    public function scopeFilterdateend($query, $end_date)
    {
        if (!empty($end_date)) {
            $query->where('date_facture', '<=', ForamatDate::normaliserDate($end_date));
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $start_date)
    {

        if (!empty($start_date)) {
            $query->where('date_facture', '>=', ForamatDate::normaliserDate($start_date));
        }
        return $query;
    }



    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function getNomClientAttribute()
    {
        return $this->client?->nom_client;
    }
    public function getNomUserAttribute()
    {
        return $this->user?->name;
    }

    public function getVoyagesAttribute()
    {
        if (empty($this->attributes['voyage_ids'])) {
            return [];
        }
        $voyageIds = json_decode($this->attributes['voyage_ids'], true);
        if (!is_array($voyageIds)) {
            return [];
        }

        return Voyage::whereIn('id', $voyageIds)->get();
    }

    public function reglements()
    {
        return $this->hasMany(FactureClientReglement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
