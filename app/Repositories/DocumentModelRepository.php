<?php

namespace App\Repositories;

use App\Models\DocumentModel;
use Illuminate\Database\Eloquent\Model;

class DocumentModelRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(DocumentModel $documentModel)
    {
        $this->model = $documentModel;
        parent::__construct($documentModel);
    }
}
