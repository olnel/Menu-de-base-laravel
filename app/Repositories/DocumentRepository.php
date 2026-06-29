<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;

class DocumentRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Document $document)
    {
        $this->model = $document;
        parent::__construct($document);
    }
}
