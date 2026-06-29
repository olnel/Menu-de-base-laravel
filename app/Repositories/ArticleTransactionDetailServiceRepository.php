<?php

namespace App\Repositories;

use App\Models\ArticleTransactionDetail;

class ArticleTransactionDetailServiceRepository extends BaseRepository
{
    public function __construct(ArticleTransactionDetail $articleTransactionDetail)
    {
        parent::__construct($articleTransactionDetail);
    }

    public function deleteByTransactionID(mixed $id)
    {
        return $this->model->where('article_transaction_id', $id)->delete();
    }
}
