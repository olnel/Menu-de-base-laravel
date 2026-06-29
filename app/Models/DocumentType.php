<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentType extends Basemodel
{
    use HasFactory;

    protected $fillable = ['nom', 'description'];

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where('nom', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%");
        }
        return $query;
    }

    public function models()
    {
        return $this->hasMany(DocumentModel::class);
    }
}
