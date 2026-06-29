<?php

namespace App\Jobs;

use App\Models\Formation;
use App\Models\Notification;
use App\Models\SessionFormation;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckFormationAlertesJob implements ShouldQueue
{
    use Queueable;

    private const URGENT_DAYS = 3;

    public function handle(): void
    {
        $today = Carbon::today();

        SessionFormation::with(['formation', 'participants.salarie'])
            ->whereNotNull('date_prochaine_formation')
            ->whereHas('formation', fn ($q) => $q->where('alerte_avant_jours', '>', 0))
            ->get()
            ->each(fn ($session) => $this->processSession($session, $today));

        Formation::with(['formationSuivante', 'sessionFormations' => function ($q) {
            $q->latest('date_formation')->limit(1);
        }])
            ->whereNotNull('formation_suivante_id')
            ->get()
            ->each(fn ($formation) => $this->processFormationChained($formation, $today));
    }

    private function processSession(SessionFormation $session, Carbon $today): void
    {
        $formation = $session->formation;
        if (!$formation) return;

        $prochaine = Carbon::parse($session->date_prochaine_formation)->startOf('day');
        $joursRestants = (int) $today->diffInDays($prochaine, false);

        if ($joursRestants < 0) {
            $this->upsertNotification($session, $formation, 'overdue', $joursRestants);
            return;
        }

        $alerteJours = $formation->alerte_avant_jours;
        $dateDebutAlerte = $prochaine->copy()->subDays($alerteJours);

        if ($today->lt($dateDebutAlerte)) {
            Notification::where('notifiable_type', SessionFormation::class)
                ->where('notifiable_id', $session->id)
                ->delete();
            return;
        }

        $type = $joursRestants <= self::URGENT_DAYS ? 'urgent' : 'warning';

        $existing = Notification::where('notifiable_type', SessionFormation::class)
            ->where('notifiable_id', $session->id)
            ->first();

        $resetReadAt = $existing?->read_at && $this->isEscalated($existing->type, $type);

        $this->upsertNotification($session, $formation, $type, $joursRestants, $resetReadAt, $existing);
    }

    private function upsertNotification(
        SessionFormation $session,
        Formation $formation,
        string $type,
        int $joursRestants,
        ?bool $resetReadAt = false,
        $existing = null,
    ): void {
        $participants = $session->participants->map(fn ($p) => [
            'id' => $p->salarie?->id,
            'nom' => $p->salarie?->nom,
            'prenom' => $p->salarie?->prenom,
        ]);

        Notification::updateOrCreate(
            [
                'notifiable_type' => SessionFormation::class,
                'notifiable_id' => $session->id,
            ],
            [
                'type' => $type,
                'titre' => $formation->nom,
                'message' => "La formation « {$formation->nom} » doit être renouvelée.",
                'jours_restants' => $joursRestants,
                'data' => [
                    'formation_id' => $formation->id,
                    'formation_nom' => $formation->nom,
                    'date_prochaine' => $session->date_prochaine_formation->format('d/m/Y'),
                    'session_id' => $session->id,
                    'participants' => $participants,
                    'alerte_avant_jours' => $formation->alerte_avant_jours,
                ],
                'read_at' => $resetReadAt ? null : ($existing?->read_at),
            ]
        );
    }

    private function processFormationChained(Formation $formation, Carbon $today): void
    {
        $derniereSession = $formation->sessionFormations->first();
        if (!$derniereSession) return;

        $suivante = $formation->formationSuivante;
        if (!$suivante) return;

        $sessionSuivanteExiste = $suivante->sessionFormations()
            ->where('date_formation', '>=', $derniereSession->date_formation)
            ->exists();

        if ($sessionSuivanteExiste) {
            Notification::where('notifiable_type', Formation::class)
                ->where('notifiable_id', $formation->id)
                ->where('type', 'formation_chainee')
                ->delete();
            return;
        }

        if ($derniereSession->created_at->lt(Carbon::now()->subDays(30))) {
            Notification::where('notifiable_type', Formation::class)
                ->where('notifiable_id', $formation->id)
                ->where('type', 'formation_chainee')
                ->delete();
            return;
        }

        $participants = $derniereSession->participants->map(fn ($p) => [
            'id' => $p->salarie?->id,
            'nom' => $p->salarie?->nom,
            'prenom' => $p->salarie?->prenom,
        ]);

        Notification::updateOrCreate(
            [
                'notifiable_type' => Formation::class,
                'notifiable_id' => $formation->id,
            ],
            [
                'type' => 'formation_chainee',
                'titre' => $suivante->nom,
                'message' => "La formation « {$suivante->nom} » (suite de {$formation->nom}) doit être planifiée.",
                'jours_restants' => null,
                'data' => [
                    'type' => 'formation_chainee',
                    'formation_origine_id' => $formation->id,
                    'formation_origine_nom' => $formation->nom,
                    'formation_suivante_id' => $suivante->id,
                    'formation_suivante_nom' => $suivante->nom,
                    'session_origine_id' => $derniereSession->id,
                    'participants' => $participants,
                ],
            ]
        );
    }

    private function isEscalated(string $from, string $to): bool
    {
        $levels = ['warning' => 1, 'urgent' => 2, 'overdue' => 3];
        return ($levels[$to] ?? 0) > ($levels[$from] ?? 0);
    }
}
