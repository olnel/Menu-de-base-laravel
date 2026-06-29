<?php

namespace App\Repositories;

use App\Models\BoncommandeFournisseurDetail;
use Illuminate\Database\Eloquent\Model;

class BoncommandeDetailRepository extends BaseRepository
{
    protected Model $model;
    public function __construct(BoncommandeFournisseurDetail $boncommandeFournisseurDetail)
    {
        $this->model = $boncommandeFournisseurDetail;
        parent::__construct($boncommandeFournisseurDetail);
    }

    public function deleteByBonCommande(mixed $id)
    {
        return $this->model->where('boncommande_fournisseur_id', $id)->delete();
    }
}
