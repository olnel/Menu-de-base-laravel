<?php

namespace App\Repositories;

use App\Models\DocumentableType;
use Illuminate\Database\Eloquent\Model;

class DocumentableTypeRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(DocumentableType $documentableType)
    {
        $this->model = $documentableType;
        parent::__construct($documentableType);
    }
}
