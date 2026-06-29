<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

class CarburantCard extends Basemodel
{
    use HasFactory, SoftDeletes;
    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = ['soldeFaible'];

    public function scopeFilter($query,$search = null)
    {
        if(!is_null($search))
        {
            $query->where(function ($q) use ($search) {
                $q->where('numero_carte', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }
    public function scopeFilterActive($query, $active)
    {
        if (!is_null($active)) {
            return $query->where('active', filter_var($active, FILTER_VALIDATE_BOOLEAN));
        }
        ;
    }


    public function mouvements()
    {
        return $this->hasMany(CarburantMouvement::class); 
    }

    public function getSoldeFaibleAttribute(): bool
    {
        return $this->solde < ($this->plafond_mensuel * 0.20);
    }

}
