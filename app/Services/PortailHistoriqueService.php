<?php

namespace App\Services;

use App\Repositories\PortailHistoriqueRepository;
use App\Services\Base\BaseService;

class PortailHistoriqueService extends BaseService
{
    protected array $relation = ['reglements'];

    protected array $scope = [
        'filter'          => 'search',
        'filterclient'    => 'client_id',
        'filterstatut'    => 'statut_facture',
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
    ];

    public function __construct(PortailHistoriqueRepository $repository)
    {
        parent::__construct($repository);
        $this->repository->setDefaultOrder('date_facture', 'desc');
    }
}
