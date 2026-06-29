<?php

namespace App\Repositories;

use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Model;

class DocumentTypeRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(DocumentType $documentType)
    {
        $this->model = $documentType;
        parent::__construct($documentType);
    }
}
