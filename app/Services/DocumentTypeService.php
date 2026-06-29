<?php

namespace App\Services;

use App\Repositories\DocumentTypeRepository;
use App\Services\Base\BaseService;

class DocumentTypeService extends BaseService
{
    protected $repository;

    public function __construct(DocumentTypeRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('nom')->setFilterValue('id');
    }
}
