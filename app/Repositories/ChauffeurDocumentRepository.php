<?php

namespace App\Repositories;

use App\Models\DocumentChauffeur;
use Illuminate\Database\Eloquent\Model;

class ChauffeurDocumentRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(DocumentChauffeur $documentChauffeur)
    {
        $this->model = $documentChauffeur;
        parent::__construct($documentChauffeur);
    }
    public function getDocumentByChauffeur(int $chauffeurId)
    {
        return $this->model->where('chauffeur_id', $chauffeurId)->get();
    }
}
