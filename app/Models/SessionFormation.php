<?php

namespace App\Models;

use App\Traits\HasNotifications;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionFormation extends Basemodel
{
    use HasNotifications;

    protected $fillable = [
        'formation_id',
        'date_formation',
        'date_prochaine_formation',
        'observation',
    ];

    protected $casts = [
        'date_formation' => 'date',
        'date_prochaine_formation' => 'date',
    ];

    /**
     * Formation parente
     */
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Participants de cette session
     */
    public function participants(): HasMany
    {
        return $this->hasMany(SessionFormationParticipant::class);
    }

    /**
     * Jours restants avant la prochaine formation
     */
    public function getJoursRestantsAttribute(): ?int
    {
        if (!$this->date_prochaine_formation) {
            return null;
        }
        return (int) Carbon::today()->diffInDays($this->date_prochaine_formation, false);
    }
}
