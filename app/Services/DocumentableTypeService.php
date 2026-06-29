<?php

namespace App\Services;

use App\Repositories\DocumentableTypeRepository;
use App\Services\Base\BaseService;

class DocumentableTypeService extends BaseService
{
    protected $repository;
    protected array $relation = ['models.documentType'];

    public function __construct(DocumentableTypeRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterLabel('nom')->setFilterValue('id');
    }
}
