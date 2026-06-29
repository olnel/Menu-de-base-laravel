<?php

namespace App\Models;

use App\Utils\ForamatDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PneuRemplacement extends Basemodel
{
    use HasFactory;

    protected $table = 'pneu_remplacements';

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('type_operation', 'LIKE', "%{$search}%")
                    ->orWhere('technicien', 'LIKE', "%{$search}%")
                    ->orWhere('motif', 'LIKE', "%{$search}%")
                    ->orWhere('observations', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicule', function ($qv) use ($search) {
                        $qv->where('immatriculation', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('pneuSerieRetire', function ($qp) use ($search) {
                        $qp->where('numero_serie', 'LIKE', "%{$search}%");
                    });
            });
        }
        return $query;
    }

    public function scopeFilterdatestart($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_operation', '>=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeFilterdateend($query, $date = null)
    {
        if (!is_null($date)) {
            $query->where('date_operation', '<=', ForamatDate::normaliserDate($date));
        }
        return $query;
    }

    public function scopeVehicule($query, $vehicule_id = null)
    {
        if (!is_null($vehicule_id)) {
            $query->where('vehicule_id', '=', $vehicule_id);
        }
        return $query;
    }

    public function scopeTypeoperation($query, $type = null)
    {
        if (!is_null($type)) {
            $query->where('type_operation', '=', $type);
        }
        return $query;
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function remorque()
    {
        return $this->belongsTo(Remorque::class, 'remorque_id');
    }

    public function pneuSerieRetire()
    {
        return $this->belongsTo(PneuSerie::class, 'pneu_serie_retire_id');
    }

    public function pneuSerieMonte()
    {
        return $this->belongsTo(PneuSerie::class, 'pneu_serie_monte_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Computed attributes pour le frontend ─────────────────────────────────

    public function getNomUserAttribute(): ?string
    {
        return $this->user?->name;
    }

    public function toArray(): array
    {
        $default = parent::toArray();
        $default['nom_user'] = $this->nom_user;
        return $default;
    }
}
