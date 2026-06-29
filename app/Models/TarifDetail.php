<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifDetail extends Basemodel
{
    use HasFactory;

    public function tarif()
    {
        return $this->belongsTo(Tarif::class,'tarif_id');
    }
}
