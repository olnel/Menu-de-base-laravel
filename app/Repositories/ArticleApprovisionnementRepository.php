<?php

namespace App\Repositories;

use App\Models\ArticleApprovisionnement;
use App\Utils\BaseQueryTemplate;

class ArticleApprovisionnementRepository extends BaseRepository
{
    public function __construct(ArticleApprovisionnement $articleApprovisionnement)
    {
        parent::__construct($articleApprovisionnement);
    }

    public function recapApprovisionnement(array $filters, array $scopes)
    {
        $query = $this->model->query();
        $query = BaseQueryTemplate::apply($query, $filters, $scopes);

        $totaux = (clone $query)->selectRaw('
            SUM(montant_ttc_appro) as total_ttc,
            SUM(montant_payer_appro) as total_paye,
            SUM(montant_reste_a_payer_appro) as total_reste_a_payer
        ')->first();

        return [
            'total_ttc' => $totaux->total_ttc ?? 0,
            'total_paye' => $totaux->total_paye ?? 0,
            'total_reste_a_payer' => $totaux->total_reste_a_payer ?? 0,
        ];
    }
}
