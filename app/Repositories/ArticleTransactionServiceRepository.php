<?php

namespace App\Repositories;

use App\Models\ArticleTransaction;

class ArticleTransactionServiceRepository extends BaseRepository
{
    public function __construct(ArticleTransaction $articleTransaction)
    {
        parent::__construct($articleTransaction);
    }
    // Your Repository methods go here
    public function find(string $first_date, string $last_date)
    {
        return $this->model
            ->whereBetween('date_transaction', [$first_date, $last_date])
            ->orderBy('id', 'DESC')
            ->first();
    }
}
