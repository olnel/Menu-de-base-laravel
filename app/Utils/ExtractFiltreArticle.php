<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltreArticle extends ExtractFiltre
{
    public static function extractFilter(Request $request): array
    {

        $baseFilters = parent::extractFilter($request);
        $article_filtre = [
            'type_article'         => $request->input('type_article'),
            'exclude_type_article' => $request->input('exclude_type_article'),
            'article_famille_id'   => $request->input('article_famille_id'),
            'magasin_id'           => $request->input('magasin_id')
        ];

        return array_merge($baseFilters, $article_filtre);
    }
}
