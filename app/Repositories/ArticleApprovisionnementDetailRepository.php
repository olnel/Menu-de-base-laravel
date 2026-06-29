<?php

namespace App\Repositories;

use App\Models\ArticleApproDetail;

class ArticleApprovisionnementDetailRepository extends BaseRepository
{
    protected \Illuminate\Database\Eloquent\Model $model;
    public function __construct(ArticleApproDetail $articleApproDetail)
    {
        $this->model= $articleApproDetail;
        parent::__construct($articleApproDetail);
    }

    public function deleteByArticleAppro(mixed $elementId)
    {
        return $this->model->where('article_approvisionnement_id', $elementId)->delete();
    }
}
