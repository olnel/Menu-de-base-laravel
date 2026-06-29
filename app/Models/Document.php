<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Basemodel
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'type',
        'documentable_type',
        'documentable_id',
        'document_type_id',
        'fichier',
        'date_expiration',
        'observation'
    ];

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where('observation', 'LIKE', "%$search%")
                  ->orWhereHas('documentType', function($q) use ($search) {
                      $q->where('nom', 'LIKE', "%$search%");
                  });
        }
        return $query;
    }

    public function documentable()
    {
        return $this->morphTo();
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }
}
