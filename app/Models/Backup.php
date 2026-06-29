<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Backup extends Basemodel
{
    use HasFactory;
    use LogsActivity;

    public string $logModule = 'backup';

    protected $fillable = [
        'filename',
        'path',
        'size',
        'type',
        'status',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search) && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
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
