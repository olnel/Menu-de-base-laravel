<?php

namespace App\Repositories;

use App\Models\Reclamation;
use App\Models\Voyage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PortailReclamationRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Reclamation $reclamation)
    {
        $this->model = $reclamation;
        parent::__construct($reclamation);
    }

    public function nextNumero(): string
    {
        $prefix = 'REC-' . now()->format('Ymd') . '-';

        $last = $this->model->newQuery()
            ->where('numero_reclamation', 'LIKE', $prefix . '%')
            ->orderByDesc('numero_reclamation')
            ->value('numero_reclamation');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getClientVoyages(int $clientId): array
    {
        return Voyage::whereHas('reservation', fn($q) => $q->where('client_id', $clientId))
            ->orderByDesc('date_voyage')
            ->get(['id', 'numero_voyage', 'depart', 'destination', 'date_voyage'])
            ->map(fn($v) => [
                'value' => $v->id,
                'label' => $v->numero_voyage . ($v->depart || $v->destination ? ' — ' . trim($v->depart . ' → ' . $v->destination, ' → ') : ''),
                'date'  => $v->date_voyage ? Carbon::parse($v->date_voyage)->format('d/m/Y') : null,
            ])
            ->toArray();
    }
}
