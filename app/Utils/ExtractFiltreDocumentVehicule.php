<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltreDocumentVehicule extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        unset($baseFilters['search']);
        $vehiculeFilters = [
            'start_date' => $request->input('start_date_document'),
            'end_date' => $request->input('end_date_document'),
            'search' => $request->input('search_document'),
            'vehicule_id' => $request->input('vehicule_id')
        ];

        $vehiculeFilters = array_filter($vehiculeFilters);

        // Validation spécifique
        if (isset($vehiculeFilters['date_expiration'])) {
            $vehiculeFilters['date_expiration'] = ForamatDate::normaliserDate($vehiculeFilters['date_expiration']);
        }

        return array_merge($baseFilters, $vehiculeFilters);
    }
}
