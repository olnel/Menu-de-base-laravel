<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltrePhotoVehicule extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        unset($baseFilters['search']);
        $vehiculeFilters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'etat_vehicule' => $request->input('etat_vehicule'),
            'type_element' => $request->input('type_element'),
            'search' => $request->input('search_photo'),
        ];

        $vehiculeFilters = array_filter($vehiculeFilters);

        // Validation spécifique
        if (isset($vehiculeFilters['date_prise_photo'])) {
            $vehiculeFilters['date_prise_photo'] = ForamatDate::normaliserDate($vehiculeFilters['date_prise_photo']);
        }

        return array_merge($baseFilters, $vehiculeFilters);
    }
}
