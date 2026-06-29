<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Tarif extends Basemodel
{
    use HasFactory;

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)){
            $query->where('nom_tarif', 'LIKE', "%{$search}%");
        }
        return $query ;
    }

    public function details(): HasMany
    {
        return $this->hasMany(TarifDetail::class, 'tarif_id');
    }
}
