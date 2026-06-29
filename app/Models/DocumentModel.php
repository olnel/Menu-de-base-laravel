<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentModel extends Basemodel
{
    use HasFactory;

    protected $fillable = [
        'documentable_type_id',
        'document_type_id',
        'obligatoire',
        'expiration_required',
        'alert_days',
        'multiple_allowed',
        'ordre',
        'actif'
    ];

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->whereHas('documentableType', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%$search%");
            })->orWhereHas('documentType', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%$search%");
            });
        }
        return $query;
    }

    public function documentableType()
    {
        return $this->belongsTo(DocumentableType::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
