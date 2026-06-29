<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepository extends BaseRepository
{

    public function __construct(Article $article)
    {
        parent::__construct($article);
    }

    public function fetchWithFilter(array $filtre)
    {

        $relation = ['magasins'];
        $scope= ['filter' => 'search', 'typearticle' => 'type_article', 'excludetypearticle' => 'exclude_type_article'];
        $orderBy = ['order_by' => 'id', 'order_direction' => 'asc'];
        return parent::fetchData(
            $relation,
            $filtre,
            $scope,
            'id',
            'reference',
            function ($query, $filtre) {
                $query->leftJoin(
                    'article_magasin',
                    function ($join) use ($filtre) {
                        $join->on('articles.id', '=', 'article_magasin.article_id');
                        if (!empty($filtre['magasin_id'])) {
                            $join->where('article_magasin.magasin_id', '=', $filtre['magasin_id']);
                        }
                    }
                );

                $query->leftJoin(
                    'magasins',
                    'article_magasin.magasin_id',
                    '=',
                    'magasins.id'
                );

                $query->select('articles.*');

                // Gestion du stock en fonction du filtre magasin
                if (empty($filtre['magasin_id'])) {
                    $query->groupBy('articles.id')
                        ->selectRaw('SUM(article_magasin.stock) as stock');
                } else {
                    $query->selectRaw('COALESCE(article_magasin.stock, 0) as stock');
                }

                // Sous-requêtes pour les pneus : disponibles (non assignés) et total série
                $query->selectRaw(
                    '(SELECT COUNT(*) FROM pneu_series'
                    . ' WHERE pneu_series.article_id = articles.id'
                    . ' AND pneu_series.deleted_at IS NULL) as pneu_stock_total'
                );
                $query->selectRaw(
                    '(SELECT COUNT(*) FROM pneu_series'
                    . ' WHERE pneu_series.article_id = articles.id'
                    . ' AND pneu_series.vehicule_id IS NULL'
                    . ' AND pneu_series.remorque_id IS NULL'
                    . ' AND pneu_series.deleted_at IS NULL) as pneu_stock_disponible'
                );

                // Filtre par catégorie
                if (!empty($filtre['article_famille_id'])) {
                    $query->where('article_famille_id', '=', $filtre['article_famille_id']);
                }
            },
            $orderBy
        );

    }
}
