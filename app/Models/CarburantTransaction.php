<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CarburantTransaction extends Basemodel
{
    use HasFactory;
    use LogsActivity;

    public string $logModule = 'carburant_transaction';

    // Recherche générique
    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where('type', 'LIKE', "%$search%")
            ->orWhere('reference','LIKE',"%$search%");
        }
        return $query;
    }

    // Dates (conformes à ExtractFiltreCarburantTransaction: start_date/end_date)
    public function scopeFilterdatestart($query, $start_date)
    {
        if (!is_null($start_date)) {
            $query->whereDate('date_transaction', '>=', $start_date);
        }
        return $query;
    }

    public function scopeFilterdateend($query, $end_date)
    {
        if (!is_null($end_date)) {
            $query->whereDate('date_transaction', '<=', $end_date);
        }
        return $query;
    }

    public function scopeFilteruser($query, $user_id = null)
    {
        if (!is_null($user_id)) {
            $query->where('user_id', $user_id);
        }
        return $query;
    }

    public function scopeFilterchauffeur($query, $chauffeur_id = null)
    {
        if (!is_null($chauffeur_id)) {
            $query->where('chauffeur_id', $chauffeur_id);
        }
        return $query;
    }

    public function scopeFiltervehicule($query, $vehicule_id = null)
    {
        if (!is_null($vehicule_id)) {
            $query->where('vehicule_id', $vehicule_id);
        }
        return $query;
    }

    public function scopeFiltercarburantcard($query, $carburant_card_id = null)
    {
        if (!is_null($carburant_card_id)) {
            $query->where('carburant_card_id', $carburant_card_id);
        }
        return $query;
    }

    // type_mvmt (conforme à ExtractFiltreCarburantTransaction)
    public function scopeFiltertypemvmt($query, $type_mvmt = null)
    {
        if (!is_null($type_mvmt)) {
            $query->where('type', $type_mvmt);
        }
        return $query;
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function carburant_card()
    {
        return $this->belongsTo(CarburantCard::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function scopeAchatsCarte($query)
    {
        return $query->where('type', 'achat_carte');
    }

    public function scopeRechargeMensusel($query)
    {
        return $query->where('type', 'recharge');
    }
    public function toArray()
    {
        $default = parent::toArray();
        $default['nom_chauffeur'] = $this->chauffeur ? strtoupper($this->chauffeur?->nom) . ' ' . ucfirst($this->chauffeur?->prenom) : null;
        $default['matricule_vehicule'] = $this->vehicule?->immatriculation;
        $default['nom_user'] = $this->user?->name;
        $default['card'] = $this->carburant_card?->numero_carte;
        $default['raw_type'] = $this->attributes['type'];
        return $default;
    }

    public function getTypeAttribute($value)
    {
        return match ($value) {
            'achat_carte' => 'Achat par Carte',
            'achat_espece' => 'Achat par Espèce',
            'recharge' => 'Recharge',
            default => $value,
        };
    }



}
