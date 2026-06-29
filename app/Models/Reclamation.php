<?php

namespace App\Models;

use App\Traits\HasNotifications;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Voyage;

class Reclamation extends Basemodel
{
    use HasFactory, HasNotifications, LogsActivity;

    public string $logModule = 'reclamation';

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_reclamation', 'LIKE', "%{$search}%")
                  ->orWhere('objet', 'LIKE', "%{$search}%")
                  ->orWhereHas('voyage', fn($v) => $v->where('numero_voyage', 'LIKE', "%{$search}%"));
            });
        }
        return $query;
    }

    public function scopeFilterclient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeFilterstatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeFilterdatestart($query, string $startDate)
    {
        return $query->where('created_at', '>=', $startDate);
    }

    public function scopeFilterdateend($query, string $endDate)
    {
        return $query->where('created_at', '<=', $endDate . ' 23:59:59');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function voyage(): BelongsTo
    {
        return $this->belongsTo(Voyage::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReclamationImage::class);
    }
}
