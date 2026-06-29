<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Basemodel
{
    use SoftDeletes;

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeForModule(Builder $query, string $notifiableType): Builder
    {
        return $query->where('notifiable_type', $notifiableType);
    }

    // ── Actions ──────────────────────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Nom court du module source (ex: "PlanningCalendar", "Voyage", "FactureClient")
     */
    public function getModuleAttribute(): string
    {
        return class_basename($this->notifiable_type);
    }
}
