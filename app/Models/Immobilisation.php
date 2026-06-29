<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Immobilisation extends Basemodel
{
    use HasFactory;

    public function scopeFilter($query, $search = null)
    {
        if(!is_null($search)) {
            $query->where('libelle', 'LIKE', "%$search%");
        }
        return $query;
    }
}
