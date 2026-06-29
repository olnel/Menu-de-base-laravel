<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltreCalendrier extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {


        $baseFilters = parent::extractFilter($request);

        $vehiculeFilters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];
        return array_merge($baseFilters,$vehiculeFilters);
    }
}
