<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\PlanningCalendar;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckMaintenanceAlertesJob implements ShouldQueue
{
    use Queueable;

    private const URGENT_DAYS = 3;

    public function handle(): void
    {
        $today = Carbon::today();

        PlanningCalendar::with(['vehicule', 'libelleMaintenance'])
            ->whereHas('libelleMaintenance', fn ($q) => $q->where('notification', '>', 0))
            ->get()
            ->each(fn ($planning) => $this->processPlanning($planning, $today));
    }

    private function processPlanning(PlanningCalendar $planning, Carbon $today): void
    {
        $info = $this->resolveAlertInfo($planning, $today);

        if (!$info) {
            Notification::where('notifiable_type', PlanningCalendar::class)
                ->where('notifiable_id', $planning->id)
                ->delete();
            return;
        }

        $existing    = Notification::where('notifiable_type', PlanningCalendar::class)
            ->where('notifiable_id', $planning->id)
            ->first();

        $resetReadAt = $existing?->read_at && $this->isEscalated($existing->type, $info['type']);

        Notification::updateOrCreate(
            [
                'notifiable_type' => PlanningCalendar::class,
                'notifiable_id'   => $planning->id,
            ],
            [
                'type'           => $info['type'],
                'titre'          => $planning->libelleMaintenance->libelle,
                'jours_restants' => $info['jours_restants'],
                'data'           => [
                    'immatriculation'  => $planning->vehicule?->immatriculation,
                    'libelle'          => $planning->libelleMaintenance?->libelle,
                    'date_maintenance' => $planning->date_maintenance,
                    'background'       => $planning->libelleMaintenance?->background,
                ],
                'read_at' => $resetReadAt ? null : ($existing?->read_at),
            ]
        );
    }

    private function resolveAlertInfo(PlanningCalendar $planning, Carbon $today): ?array
    {
        if (!$planning->libelleMaintenance) {
            return null;
        }

        $due           = Carbon::parse($planning->date_maintenance)->startOf('day');
        $daysRemaining = (int) $today->diffInDays($due, false);

        if ($daysRemaining < 0) {
            return ['type' => 'overdue', 'jours_restants' => $daysRemaining];
        }

        $windowStart = $due->copy()->subDays($planning->libelleMaintenance->notification);

        if ($today->lt($windowStart)) {
            return null;
        }

        return [
            'type'           => $daysRemaining <= self::URGENT_DAYS ? 'urgent' : 'warning',
            'jours_restants' => $daysRemaining,
        ];
    }

    private function isEscalated(string $from, string $to): bool
    {
        $levels = ['warning' => 1, 'urgent' => 2, 'overdue' => 3];
        return ($levels[$to] ?? 0) > ($levels[$from] ?? 0);
    }
}
