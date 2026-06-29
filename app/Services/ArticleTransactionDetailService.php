<?php

namespace App\Services;

use App\Repositories\ArticleTransactionDetailServiceRepository;
use App\Services\Base\BaseService;

class ArticleTransactionDetailService extends BaseService
{
    protected $repository;
    public function __construct(ArticleTransactionDetailServiceRepository $articleTransactionDetailServiceRepository)
    {
        $this->repository = $articleTransactionDetailServiceRepository;
        parent::__construct($articleTransactionDetailServiceRepository);
    }

    public function deleteByIDTransaction(mixed $id): void
    {
        $this->repository->deleteByTransactionID($id);
    }
}
