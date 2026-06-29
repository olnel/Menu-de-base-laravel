<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Models\Voyage;
use App\Repositories\PortailEvaluationRepository;
use App\Services\Base\BaseService;
use Illuminate\Support\Facades\DB;

class PortailEvaluationService extends BaseService
{
    protected array $relation = ['voyage', 'chauffeur'];

    protected array $scope = [
        'filter'          => 'search',
        'filterclient'    => 'client_id',
        'filternote'      => 'note',
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
    ];

    public function __construct(PortailEvaluationRepository $repository)
    {
        parent::__construct($repository);
        $this->repository->setDefaultOrder('created_at', 'desc');
    }

    public function store(int $clientId, array $validated): Evaluation
    {
        return DB::transaction(function () use ($clientId, $validated) {
            $voyage     = Voyage::findOrFail($validated['voyage_id']);
            $chauffeurId = $voyage->chauffeur_id;

            return $this->repository->create([
                'client_id'          => $clientId,
                'voyage_id'          => $validated['voyage_id'],
                'chauffeur_id'       => $chauffeurId,
                'numero_evaluation'  => $this->repository->nextNumero(),
                'note'               => $validated['note'],
                'commentaire'        => $validated['commentaire'] ?? null,
            ]);
        });
    }

    public function hasEvaluatedVoyage(int $clientId, int $voyageId): bool
    {
        return $this->repository->hasEvaluatedVoyage($clientId, $voyageId);
    }

    public function getStats(int $clientId): array
    {
        return $this->repository->getStats($clientId);
    }

    public function getClientVoyagesNonEvalues(int $clientId): array
    {
        return $this->repository->getClientVoyagesNonEvalues($clientId);
    }
}
