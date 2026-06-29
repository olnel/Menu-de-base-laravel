<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentableType extends Basemodel
{
    use HasFactory;

    protected $fillable = ['nom', 'model_class'];

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where('nom', 'LIKE', "%$search%")
                  ->orWhere('model_class', 'LIKE', "%$search%");
        }
        return $query;
    }

    public function models()
    {
        return $this->hasMany(DocumentModel::class);
    }
}
