<?php

namespace App\Services;

use App\Repositories\ReclamationRepository;
use App\Services\Base\BaseService;

class ReclamationService extends BaseService
{
    protected array $relation = ['client', 'voyage', 'images'];

    protected array $scope = [
        'filter'          => 'search',
        'filterclient'    => 'client_id',
        'filterstatut'    => 'statut',
        'filterdatestart' => 'start_date',
        'filterdateend'   => 'end_date',
    ];

    public function __construct(ReclamationRepository $repository)
    {
        parent::__construct($repository);
        $this->repository->setDefaultOrder('created_at', 'desc');
    }
}
