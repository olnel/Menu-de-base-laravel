<?php

namespace App\Utils\Portail;

use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;

class ExtractFiltrePortailHistorique extends ExtractFiltre
{
    public static function extractFilter(Request $request): array
    {
        $baseFilters = parent::extractFilter($request);

        $specific = array_filter([
            'start_date'     => $request->input('start_date'),
            'end_date'       => $request->input('end_date'),
            'statut_facture' => $request->input('statut_facture'),
        ]);

        return array_merge($baseFilters, $specific);
    }
}
