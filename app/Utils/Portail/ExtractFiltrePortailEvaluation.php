<?php

namespace App\Utils\Portail;

use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;

class ExtractFiltrePortailEvaluation extends ExtractFiltre
{
    public static function extractFilter(Request $request): array
    {
        $baseFilters = parent::extractFilter($request);

        $specific = array_filter([
            'note'       => $request->input('note'),
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ]);

        return array_merge($baseFilters, $specific);
    }
}
