<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltreReclamation extends ExtractFiltre
{
    public static function extractFilter(Request $request): array
    {
        $baseFilters = parent::extractFilter($request);

        $specific = array_filter([
            'statut'     => $request->input('statut'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ], fn($v) => $v !== null && $v !== '');

        return array_merge($baseFilters, $specific);
    }
}
