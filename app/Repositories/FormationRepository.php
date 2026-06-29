<?php

namespace App\Repositories;

use App\Models\SessionFormation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class FormationRepository extends BaseRepository
{
    public function __construct(\App\Models\Formation $formation)
    {
        parent::__construct($formation);
        $this->setDefaultOrder('nom', 'asc');
    }

    /**
     * Récupère les sessions de formation dont la date de prochaine formation
     * est dans la fenêtre d'alerte
     */
    public function getSessionsEnAlerte(): Collection
    {
        $today = Carbon::today();

        return SessionFormation::with([
            'formation',
            'participants.salarie',
        ])
            ->whereNotNull('date_prochaine_formation')
            ->whereHas('formation', function ($q) {
                $q->where('alerte_avant_jours', '>', 0);
            })
            ->get()
            ->filter(function (SessionFormation $session) use ($today) {
                $prochaine = Carbon::parse($session->date_prochaine_formation);
                $alerteJours = $session->formation->alerte_avant_jours;
                $dateAlerte = $prochaine->copy()->subDays($alerteJours);
                return $today->greaterThanOrEqualTo($dateAlerte);
            });
    }

    /**
     * Récupère les formations qui ont une formation suivante
     * et dont la dernière session a été créée récemment
     */
    public function getFormationsAvecSuiteRecente(): Collection
    {
        return \App\Models\Formation::with([
            'formationSuivante',
            'sessionFormations' => function ($q) {
                $q->latest('date_formation')->limit(1);
            },
        ])
            ->whereNotNull('formation_suivante_id')
            ->get()
            ->filter(function (\App\Models\Formation $formation) {
                $derniereSession = $formation->sessionFormations->first();
                if (!$derniereSession) return false;
                // Alerter si la session a été créée dans les 7 derniers jours
                return $derniereSession->created_at->greaterThanOrEqualTo(Carbon::now()->subDays(7));
            });
    }

    /**
     * Récupère les participants d'une session de formation
     */
    public function getParticipantsParSession(int $sessionFormationId): Collection
    {
        return \App\Models\SessionFormationParticipant::with('salarie')
            ->where('session_formation_id', $sessionFormationId)
            ->get();
    }
}
