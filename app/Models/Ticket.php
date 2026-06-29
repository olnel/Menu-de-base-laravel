<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Basemodel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function reparationVehicule()
    {
        return $this->belongsTo(ReparationVehicule::class);
    }

    public function reparationVehiculeArticle()
    {
        return $this->belongsTo(ReparationVehiculeArticle::class);
    }

    public function reparationVehiculeArticleDetail()
    {
        return $this->belongsTo(ReparationVehiculeArticleDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
