<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Basemodel
{
    use HasFactory, LogsActivity;

    public string $logModule = 'evaluation';

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_evaluation', 'LIKE', "%{$search}%")
                  ->orWhereHas('voyage', fn($v) => $v->where('numero_voyage', 'LIKE', "%{$search}%"))
                  ->orWhereHas('chauffeur', fn($c) => $c->where('nom', 'LIKE', "%{$search}%")
                      ->orWhere('prenom', 'LIKE', "%{$search}%"));
            });
        }
        return $query;
    }

    public function scopeFilterclient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeFilternote($query, ?int $note)
    {
        if (!is_null($note)) {
            $query->where('note', $note);
        }
        return $query;
    }

    public function scopeFilterdatestart($query, ?string $date)
    {
        if (!empty($date)) {
            $query->where('created_at', '>=', $date);
        }
        return $query;
    }

    public function scopeFilterdateend($query, ?string $date)
    {
        if (!empty($date)) {
            $query->where('created_at', '<=', $date . ' 23:59:59');
        }
        return $query;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function voyage(): BelongsTo
    {
        return $this->belongsTo(Voyage::class);
    }

    public function chauffeur(): BelongsTo
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
