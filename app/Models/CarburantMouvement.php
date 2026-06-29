<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CarburantMouvement extends Basemodel
{
    use HasFactory;
    protected $table = 'carburant_mouvements';
    public $cast = ['qte_mvmt' => 'float'];
    // Recherche générique
    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type', 'LIKE', "%$search%")
                    ->orWhere('reference_mvmt', 'LIKE', "%$search%");
            });
        }
        return $query;
    }

    // Dates (start/end) alignées avec une logique d'extracteur standard
    public function scopeFilterdatestart($query, $start_date)
    {
        if (!is_null($start_date)) {
            $query->whereDate('date_mvmt', '>=', $start_date);
        }
        return $query;
    }

    public function scopeFilterdateend($query, $end_date)
    {
        if (!is_null($end_date)) {
            $query->whereDate('date_mvmt', '<=', $end_date);
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

    public function scopeFiltercarburantcard($query, $carburant_card_id = null)
    {
        if (!is_null($carburant_card_id)) {
            $query->where('carburant_card_id', $carburant_card_id);
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

    public function scopeFilterchauffeur($query, $chauffeur_id = null)
    {
        if (!is_null($chauffeur_id)) {
            $query->where('chauffeur_id', $chauffeur_id);
        }
        return $query;
    }

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
        return $default;
    }

    public function getTypeAttribute($value)
    {
        return match ($value) {
            'achat_carte' => 'Achat par Carte',
            'achat_espece' => 'Achat par Espèce',
            'recharge' => 'Recharge',
            'ajustement' => 'Ajustement',
            'annulation_transaction' => 'Annulation Transaction',
            default => $value,
        };
    }
}
