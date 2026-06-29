<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsPosition extends Model
{
    protected $fillable = [
        'vehicule_id',
        'latitude',
        'longitude',
        'vitesse',
        'heading',
        'gps_at',
        'raw_data',
    ];

    protected $casts = [
        'gps_at'    => 'datetime',
        'latitude'  => 'float',
        'longitude' => 'float',
        'vitesse'   => 'float',
        'heading'   => 'integer',
        'raw_data'  => 'array',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
}
