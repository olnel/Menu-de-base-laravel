<?php

namespace App\Repositories;

use App\Models\BoncommandeFournisseur;
use Illuminate\Database\Eloquent\Model;

class BonCommandeRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(BoncommandeFournisseur $boncommandeFournisseur)
    {
        $this->model = $boncommandeFournisseur;
        parent::__construct($boncommandeFournisseur);
    }
    // Your Repository methods go here
    public function find(string $firstDate, string $lastDate)
    {
        return $this->model
            ->whereBetween('date_boncommande', [$firstDate, $lastDate])
            ->orderBy('id', 'DESC')
            ->first();
    }
}
