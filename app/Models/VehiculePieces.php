<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculePieces extends Basemodel
{
    use HasFactory;

    public function proprietaire()
    {
        return $this->morphTo();
    }
}
