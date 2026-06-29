<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Basemodel
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search) && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                    ->orWhere('module', 'LIKE', "%{$search}%")
                    ->orWhere('user_name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeFilterAction($query, $action = null)
    {
        if (!is_null($action) && $action !== '') {
            $query->where('action', $action);
        }
        return $query;
    }

    public function scopeFilterModule($query, $module = null)
    {
        if (!is_null($module) && $module !== '') {
            $query->where('module', $module);
        }
        return $query;
    }

    public function scopeFilterUser($query, $userId = null)
    {
        if (!is_null($userId) && $userId !== '') {
            $query->where('user_id', $userId);
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date) && $date !== '') {
            $query->whereDate('created_at', '>=', $date);
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date) && $date !== '') {
            $query->whereDate('created_at', '<=', $date);
        }
        return $query;
    }
}
