<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ParamElement extends Basemodel
{
    use HasFactory;

    public function scopeFilter($query, $search = null)
    {
        if(!is_null($search)) {
            $query->where('type_model', 'LIKE', "%$search%");
        }
        return $query ;
    }

    public function details()
    {
        return $this->hasMany(ParamElementDetail::class,'param_element_id');
    }
}
