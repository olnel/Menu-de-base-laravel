<?php

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Services\Base\BaseService;

class ArticleService extends BaseService
{
    protected $repository;
    protected $articleFamille_service;
    protected $pneuSerieService;


    protected array $scope = [
        'filter'               => 'search',
        //        'magasin' => 'magasin_id',
        'famillearticle'       => 'article_famille_id',
        'typearticle'          => 'type_article',
        'excludetypearticle'   => 'exclude_type_article',
        'filterdatestart'      => 'start_date',
        'filterdateend'        => 'end_date'
    ];

    public function __construct(ArticleRepository $articleRepository, ArticleFamilleService $articleService, PneuSerieService $pneuSerieService)
    {
        $this->repository = $articleRepository;
        $this->articleFamille_service = $articleService;
        $this->pneuSerieService = $pneuSerieService;

        parent::__construct($articleRepository);
    }

    public function fetchData(array $filters = [])
    {
        return $this->repository->fetchWithFilter($filters);
    }

    // Your service methods go here
    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')->setFilterLabel('nom_famille_article');
    }

    public function create(array $validated): array
    {
        $dataFamilleArticle = ['nom_famille_article' => $validated['nom_famille_article']];
        // insertion dans la table famille_article
        $article_famille_id = $this->articleFamille_service->isExiste($dataFamilleArticle);

        unset($validated['nom_famimlle_article']);
        $validated['article_famille_id'] = $article_famille_id;

        // Mise en majuscule du type d'article
        if (isset($validated['type_article'])) {
            $validated['type_article'] = mb_strtoupper($validated['type_article'], 'UTF-8');
        }

        $output = parent::create($validated);
        return $output;
    }

    public function update($model, array $validated): array
    {
        $dataFamilleArticle = ['nom_famille_article' => $validated['nom_famille_article']];
        // insertion dans la table famille_article
        $article_famille_id = $this->articleFamille_service->isExiste($dataFamilleArticle);

        unset($validated['nom_famimlle_article']);
        $validated['article_famille_id'] = $article_famille_id;

        // Mise en majuscule du type d'article
        if (isset($validated['type_article'])) {
            $validated['type_article'] = mb_strtoupper($validated['type_article'], 'UTF-8');
        }

        $output = parent::update($model, $validated);
        return $output;

    }

    /**
     * Récupère les séries de pneus d'un article avec détails complets
     */
    public function getPneuSeries($article)
    {
        if (strtolower($article->type_article) !== 'pneu') {
            return ['error' => true, 'message' => 'Cet article n\'est pas un pneu'];
        }

        $series = $this->pneuSerieService->getSeriesDetailsByArticle($article->id);

        $total      = $series->count();
        $disponible = $series->filter(fn($s) => $s['vehicule'] === null && $s['remorque'] === null)->count();
        $en_service = $series->filter(fn($s) => $s['vehicule'] !== null || $s['remorque'] !== null)->count();
        $a_remplacer= $series->filter(fn($s) => $s['etat'] === 'a_remplacer')->count();

        return [
            'error' => false,
            'data'  => [
                'article' => [
                    'id'          => $article->id,
                    'reference'   => $article->reference,
                    'designation' => $article->designation,
                    'marque'      => $article->marque,
                ],
                'stats' => [
                    'total'       => $total,
                    'disponible'  => $disponible,
                    'en_service'  => $en_service,
                    'a_remplacer' => $a_remplacer,
                ],
                'series' => $series->values(),
            ]
        ];
    }

}
