<?php

namespace App\Services;

use App\Repositories\ArticleMouvementRepository;
use App\Services\Base\BaseService;

class ArticleMouvementService extends BaseService
{
    protected $repository;
    protected array $scope =  ['filter' => 'search', 'magasin' => 'magasin_id', 'famillearticle' => 'article_famille_id', 'filterdatestart' => 'start_date', 'filterdateend' => 'end_date'];

    private $article_magasin ;
    public function __construct(ArticleMouvementRepository $articleMouvementRepository, ArticlemagasinService $articlemagasinService)
    {
        $this->repository = $articleMouvementRepository;
        $this->article_magasin = $articlemagasinService;
        parent::__construct($this->repository);
    }

    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('reference_mvt');
    }

    private function findArticleMagasin($critere)
    {
      return $this->article_magasin->findElement($critere);
    }

    public function entrerMouvement($data)
    {
        $critere = ['article_id'=> $data['article_id'], 'magasin_id' => $data['magasin_id']];

        $article_magasin_info = $this->findArticleMagasin($critere);
        $stock =  $article_magasin_info->stock ?? 0;
        $data['qte_initial'] = $stock;
        $data['qte_final'] = $stock + $data['qte_mvt'];

        parent::create($data);

        // Modification des stock
        $this->article_magasin->updateOrCreateStock($data['article_id'], $data['magasin_id'],  $data['qte_final']);
    }

    public function sortieMouvement($data)
    {
        $critere = ['article_id'=> $data['article_id'], 'magasin_id' => $data['magasin_id']];

        $article_magasin_info = $this->findArticleMagasin($critere);
        $stock =  $article_magasin_info->stock ?? 0;
        $data['qte_initial'] = $stock;
        $data['qte_final'] = $stock - $data['qte_mvt'];
        parent::create($data);

        $this->article_magasin->updateOrCreateStock($data['article_id'], $data['magasin_id'],  $data['qte_final']);
    }

    public function annulationMouvement()
    {
        
    }
}
