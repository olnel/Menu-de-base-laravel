<?php

namespace App\Repositories;

use App\Models\PlanningCalendar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class PlanningCalendarRepository extends BaseRepository
{
    public function __construct(PlanningCalendar $planningCalendar)
    {
        parent::__construct($planningCalendar);
    }

    /**
     * Récupère les maintenances à notifier
     */
    public function getNotificationsToDisplay(): Collection
    {
        return $this->model->with(['vehicule', 'libelleMaintenance'])
            ->whereHas('libelleMaintenance', function($query) {
                $query->where('notification', '>', 0);
            })
            ->get()
            ->filter(function($maintenance) {
                return $maintenance->shouldNotifyToday();
            })
            ->values();
    }

    /**
     * Version alternative avec requête SQL
     */
    public function getNotificationsToDisplaySql(): Collection
    {
        $today = Carbon::today()->format('Y-m-d');

        return $this->model->with(['vehicule', 'libelleMaintenance'])
            ->whereHas('libelleMaintenance', function($query) {
                $query->where('notification', '>', 0);
            })
            ->whereRaw('? BETWEEN DATE_SUB(date_maintenance, INTERVAL libelle_maintenances.notification DAY) AND date_maintenance', [$today])
            ->orderBy('date_maintenance')
            ->get();
    }

}
