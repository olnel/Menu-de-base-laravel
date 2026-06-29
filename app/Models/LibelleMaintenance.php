<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LibelleMaintenance extends Basemodel
{
    use HasFactory;

    public function scopeFilter($query, $search = null)
    {
        if (!is_null($search)){
            $query->where(function($q) use ($search) {
                $q->where('libelle', 'LIKE', "%{$search}%")
                    ->orWhere('notification', 'LIKE', "%{$search}%")
                    ->orWhere('background', 'LIKE', "%{$search}%");

            });
        }
        return $query;
    }

    /**
     * Calcule la date de début de notification
     */
    public function getNotificationStartDate($maintenanceDate): Carbon
    {
        return Carbon::parse($maintenanceDate)->subDays($this->notification);
    }

    public function planningCalendars()
    {
        return $this->hasMany(PlanningCalendar::class,'libelle_maintenance_id');
    }
}
