<?php

namespace App\Repositories;

use App\Models\DevisClient;

class DevisClientRepository extends BaseRepository
{
    public function __construct(DevisClient $devisClient)
    {
        parent::__construct($devisClient);
    }

    public function find(string $firstDate, string $lastDate)
    {
        return $this->model
            ->whereBetween('date_devis', [$firstDate, $lastDate])
            ->orderBy('id', 'DESC')
            ->first();
    }

}
