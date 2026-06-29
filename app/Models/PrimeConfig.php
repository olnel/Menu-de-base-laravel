<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrimeConfig extends Basemodel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'libelle',
        'montant',
        'type_salarie_id',
        'is_global',
        'is_per_voyage',
        'is_actif',
    ];

    public function typeSalarie()
    {
        return $this->belongsTo(TypeSalarie::class);
    }

    public function scopeFilter($query, $search)
    {
        if ($search) {
            $query->where('libelle', 'LIKE', "%$search%");
        }
        return $query;
    }
}
