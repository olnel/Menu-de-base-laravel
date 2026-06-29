<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParamElementDetail extends Basemodel
{
    use HasFactory;

    public function paramElement()
    {
        return $this->belongsTo(ParamElement::class,'param_element_id');
    }
}
