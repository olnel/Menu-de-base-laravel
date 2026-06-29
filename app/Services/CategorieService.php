<?php

namespace App\Services;

use App\Repositories\CategorieRepository;
use App\Services\Base\BaseService;


class CategorieService extends BaseService
{
    public function __construct(CategorieRepository $categorieRepository)
    {
        parent::__construct($categorieRepository);
    }
    protected function initializeFilters(): void
    {
       $this->setFilterValue('nom')->setFilterLabel('title');
    }
}
