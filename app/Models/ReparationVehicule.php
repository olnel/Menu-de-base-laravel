<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Override;

class ReparationVehicule extends Basemodel
{
    use HasFactory;

    protected $guarded = ["id"];

    public function articles()
    {
        return $this->hasMany(ReparationVehiculeArticle::class);
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Salarie::class, "responsable_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            "created_by_name" => $this->user?->name,
            "responsable_name" => $this->responsable
                ? trim(
                    $this->responsable->nom . " " . $this->responsable->prenom,
                )
                : null,
            "date_enreg" => $this->created_at?->format("d/m/Y à H:i"),
            "have_ticket" => $this->tickets()->exists(), 
        ]);
    }
}
