<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionFormationParticipant extends Basemodel
{
    protected $fillable = [
        'session_formation_id',
        'salarie_id',
    ];

    /**
     * Session de formation
     */
    public function sessionFormation(): BelongsTo
    {
        return $this->belongsTo(SessionFormation::class);
    }

    /**
     * Salarié participant
     */
    public function salarie(): BelongsTo
    {
        return $this->belongsTo(Salarie::class);
    }
}
