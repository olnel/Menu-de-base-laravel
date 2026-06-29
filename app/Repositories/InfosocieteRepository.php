<?php

namespace App\Repositories;

use App\Models\InfoSociete;
use Illuminate\Database\Eloquent\Model;

class InfosocieteRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(InfoSociete $infoSociete)
    {
        $this->model = $infoSociete;
        parent::__construct($infoSociete);
    }

    public function fetchElement()
    {
        return $this->model->findOrFail(1);
    }
}
