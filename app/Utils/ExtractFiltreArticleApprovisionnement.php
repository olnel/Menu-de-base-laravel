<?php

namespace App\Utils;
use Illuminate\Http\Request;
class ExtractFiltreArticleApprovisionnement extends ExtractFiltreArticleInventaire
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        unset($baseFilters['article_famille_id']);
        $baseFilters['nom_fournisseur'] = $request->input('nom_fournisseur');
        $baseFilters['statut'] = $request->input('statut');

        return $baseFilters;

    }
}
