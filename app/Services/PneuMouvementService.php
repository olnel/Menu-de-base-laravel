<?php

namespace App\Services;

use App\Repositories\PneuMouvementRepository;
use App\Services\Base\BaseService;
use Intervention\Image\Origin;

class PneuMouvementService extends BaseService
{
    protected $repository;
    protected array $scope = ['filter' => 'search', 'magasin' => 'magasin_id', 'filterdatestart' => 'start_date', 'filterdateend' => 'end_date'];

    private $article_magasin;

    public function __construct(PneuMouvementRepository $pneuMouvementRepository, ArticlemagasinService $articlemagasinService)
    {
        $this->repository = $pneuMouvementRepository;
        $this->article_magasin = $articlemagasinService;
        parent::__construct($this->repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('reference_mvt');
    }


    public function entrerMouvement(array $data): void
    {
        parent::create($data);
    }

    public function sortieMouvement(array $data): void
    {
        parent::create($data);
    }
    }
