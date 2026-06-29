<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltreCarburantCard extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {


        $baseFilters = parent::extractFilter($request);

        $vehiculeFilters = [
            'active' => $request->active
        ];
        return array_merge($baseFilters, $vehiculeFilters);
    }
}
