<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractTresorerieMouvement extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        $filter = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'tresorerie_id' => $request->input('tresorerie_id'),
            'user_id' => $request->input('user_id'),
        ];
        $filtre = array_filter($filter);

        return array_merge($baseFilters, $filtre);
    }
}
