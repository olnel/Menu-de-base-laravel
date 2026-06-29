<?php

namespace App\Repositories;

use App\Models\Reclamation;
use Illuminate\Database\Eloquent\Model;

class ReclamationRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Reclamation $reclamation)
    {
        $this->model = $reclamation;
        parent::__construct($reclamation);
        $this->setDefaultOrder('created_at', 'desc');
    }
}
