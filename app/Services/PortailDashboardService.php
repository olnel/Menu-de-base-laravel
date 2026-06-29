<?php

namespace App\Services;

use App\Repositories\PortailDashboardRepository;
use App\Services\Base\BaseService;

class PortailDashboardService extends BaseService
{
    protected array $scope = [
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
        'filterclient'    => 'client_id',
        'filteretat'      => 'etat_facture',
    ];

    public function __construct(PortailDashboardRepository $repository)
    {
        parent::__construct($repository);
    }

    public function getStats(array $filters): array
    {
        $base = array_diff_key($filters, ['etat_facture' => '']);

        return [
            'total'      => $this->countElement($base),
            'en_attente' => $this->countElement(array_merge($base, ['etat_facture' => 'non_valide'])),
            'validees'   => $this->countElement(array_merge($base, ['etat_facture' => 'valide'])),
        ];
    }

    public function getRecentes(int $clientId, int $limit = 5)
    {
        return $this->repository->getRecentes($clientId, $limit);
    }

    public function getFactureStats(array $filters): array
    {
        return $this->repository->getFactureStats(
            $filters['client_id'],
            $filters['start_date'] ?? null,
            $filters['end_date']   ?? null,
        );
    }

    public function getRecentesFactures(int $clientId, int $limit = 3)
    {
        return $this->repository->getRecentesFactures($clientId, $limit);
    }

    public function getReclamationStats(array $filters): array
    {
        return $this->repository->getReclamationStats(
            $filters['client_id'],
            $filters['start_date'] ?? null,
            $filters['end_date']   ?? null,
        );
    }

    public function getRecentesReclamations(int $clientId, int $limit = 3)
    {
        return $this->repository->getRecentesReclamations($clientId, $limit);
    }

    public function getEvaluationStats(array $filters): array
    {
        return $this->repository->getEvaluationStats(
            $filters['client_id'],
            $filters['start_date'] ?? null,
            $filters['end_date']   ?? null,
        );
    }

    public function getRecentesEvaluations(int $clientId, int $limit = 3)
    {
        return $this->repository->getRecentesEvaluations($clientId, $limit);
    }
}
