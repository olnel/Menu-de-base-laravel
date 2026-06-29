<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChauffeurVehicule extends Model
{
    use HasFactory;
    protected $table = 'chauffeur_vehicule';
    protected $guarded = ["id"];

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
}
