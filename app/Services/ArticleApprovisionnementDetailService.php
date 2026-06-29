<?php

namespace App\Services;

use App\Repositories\ArticleApprovisionnementDetailRepository;
use App\Services\Base\BaseService;

class ArticleApprovisionnementDetailService extends BaseService
{
    protected $repository;
    public function __construct(ArticleApprovisionnementDetailRepository $articleApprovisionnementRepository)
    {
        $this->repository = $articleApprovisionnementRepository;
        parent::__construct($articleApprovisionnementRepository);
    }

    public function deleteByArticleAppro(mixed $id)
    {
        $this->repository->deleteByArticleAppro($id);
    }
}
