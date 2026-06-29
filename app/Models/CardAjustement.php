<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardAjustement extends Basemodel
{
    use HasFactory;
    public function mouvement()
    {
        return $this->belongsTo(CarburantMouvement::class, 'carburant_mouvement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
