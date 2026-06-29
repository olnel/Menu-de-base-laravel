<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltrePneuInventaire extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {
        $baseFilters = parent::extractFilter($request);

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'magasin_id' => $request->input('magasin_id'),
            'article_id' => $request->input('article_id'),
        ];

        $filters = array_filter($filters);
        return array_merge($baseFilters, $filters);
    }
}


