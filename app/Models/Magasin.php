<?php

namespace App\Models;

use App\Traits\HasValueText;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Magasin extends Basemodel
{
    use HasFactory;
    use HasValueText;

    public function scopeFilter($query, $search = null)
    {
        if(!is_null($search)) {
            $query->where('nom_magasin', 'LIKE', "%$search%");
        }
        return $query;
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class)
                    ->withPivot(['stock', 'deleted_at'])
                    ->withTimestamps();
    }
}
