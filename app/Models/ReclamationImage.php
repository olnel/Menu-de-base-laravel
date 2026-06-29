<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReclamationImage extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return '/' . $this->chemin;
    }

    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(Reclamation::class);
    }
}
