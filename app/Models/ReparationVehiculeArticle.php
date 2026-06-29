<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReparationVehiculeArticle extends Basemodel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function details()
    {
        return $this->hasMany(ReparationVehiculeArticleDetail::class);
    }

    public function reparationVehicule()
    {
        return $this->belongsTo(ReparationVehicule::class);
    }
}
