<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehiculeElement extends Basemodel
{
    use HasFactory;

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function pneuSerie()
    {
        return $this->belongsTo(PneuSerie::class, 'numero_serie', 'numero_serie');
    }

    public function toArray()
    {
        $arr = parent::toArray();
        if ($this->is_pneu && $this->numero_serie) {
            $this->loadMissing('pneuSerie');
            $arr['date_montage'] = $this->pneuSerie?->date_montage;
            $arr['is_first']     = (bool) ($this->pneuSerie?->is_first ?? false);
        } else {
            $arr['date_montage'] = null;
            $arr['is_first']     = false;
        }
        return $arr;
    }
}
