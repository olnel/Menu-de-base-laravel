<?php

namespace App\Repositories;

use App\Models\Evaluation;
use App\Models\Voyage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PortailEvaluationRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Evaluation $evaluation)
    {
        $this->model = $evaluation;
        parent::__construct($evaluation);
    }

    public function nextNumero(): string
    {
        $prefix = 'EVL-' . now()->format('Ymd') . '-';

        $last = $this->model->newQuery()
            ->where('numero_evaluation', 'LIKE', $prefix . '%')
            ->orderByDesc('numero_evaluation')
            ->value('numero_evaluation');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function hasEvaluatedVoyage(int $clientId, int $voyageId): bool
    {
        return $this->model->newQuery()
            ->where('client_id', $clientId)
            ->where('voyage_id', $voyageId)
            ->exists();
    }

    public function getStats(int $clientId): array
    {
        $evaluations = $this->model->newQuery()
            ->where('client_id', $clientId)
            ->whereNull('deleted_at');

        return [
            'total'         => $evaluations->count(),
            'note_moyenne'  => round((float) $evaluations->avg('note'), 1),
            'par_note'      => $evaluations->clone()
                ->selectRaw('note, COUNT(*) as total')
                ->groupBy('note')
                ->pluck('total', 'note')
                ->toArray(),
        ];
    }

    public function getClientVoyagesNonEvalues(int $clientId): array
    {
        $voyagesEvalues = $this->model->newQuery()
            ->where('client_id', $clientId)
            ->pluck('voyage_id')
            ->toArray();

        return Voyage::whereHas('reservation', fn($q) => $q->where('client_id', $clientId))
            ->whereNotIn('id', $voyagesEvalues)
            ->whereNotNull('chauffeur_id')
            ->orderByDesc('date_voyage')
            ->get(['id', 'numero_voyage', 'depart', 'destination', 'date_voyage', 'chauffeur_id'])
            ->load('chauffeur')
            ->map(fn($v) => [
                'value'       => $v->id,
                'label'       => $v->numero_voyage . ($v->depart || $v->destination
                    ? ' — ' . trim($v->depart . ' → ' . $v->destination, ' → ')
                    : ''),
                'date'        => $v->date_voyage ? Carbon::parse($v->date_voyage)->format('d/m/Y') : null,
                'chauffeur'   => $v->chauffeur ? [
                    'id'  => $v->chauffeur->id,
                    'nom' => strtoupper($v->chauffeur->nom) . ' ' . ucfirst($v->chauffeur->prenom ?? ''),
                ] : null,
            ])
            ->toArray();
    }
}
