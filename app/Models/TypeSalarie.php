<?php

namespace App\Models;

use App\Traits\HasValueText;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeSalarie extends Basemodel
{
    use HasFactory;
    use HasValueText;

    protected $fillable = [
        'libelle',
        'description',
    ];

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where('libelle', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%");
        }
        return $query;
    }
}
