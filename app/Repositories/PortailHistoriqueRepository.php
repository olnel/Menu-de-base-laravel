<?php

namespace App\Repositories;

use App\Models\FactureClient;
use Illuminate\Database\Eloquent\Model;

class PortailHistoriqueRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(FactureClient $factureClient)
    {
        $this->model = $factureClient;
        parent::__construct($factureClient);
    }
}
