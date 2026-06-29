<?php

namespace App\Services;

use App\Repositories\ArticlemagasinRepository;
use App\Services\Base\BaseService;
use function Termwind\parse;

class ArticlemagasinService extends BaseService
{

    protected $repository;
    public function __construct(ArticlemagasinRepository $articlemagasinRepository)
    {
        $this->repository = $articlemagasinRepository;
        parent::__construct($articlemagasinRepository);
    }

    public function updateOrCreateStock(mixed $article_id, mixed $magasin_id, mixed $stock_reel)
    {
        $element = $this->repository->findElement(['article_id' => $article_id, 'magasin_id' => $magasin_id]);

        if ($element) {

            parent::update($element, ['stock' => $stock_reel]);
        }else{
            parent::create(['article_id'=> $article_id, 'magasin_id' => $magasin_id, 'stock' => $stock_reel]);
        }
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('article_id')->setFilterLabel('article_id');
    }

    /**
     * fonction permet de faire un filtre article Magasin
     * @param $critere
     * @return mixed
     */
    public function findElement($critere)
    {
        return $this->repository->findElement($critere);
    }
}
