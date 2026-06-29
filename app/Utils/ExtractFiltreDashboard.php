<?php

namespace App\Utils;
use Illuminate\Http\Request;
class ExtractFiltreDashboard extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {


        $baseFilters = parent::extractFilter($request);
        unset($baseFilters['per_page']);
        unset($baseFilters['current_page']);
        $vehiculeFilters = [
            'start_date' => $request->input('start_date') ?? date('Y-m-01'),
            'end_date' => $request->input('end_date') ?? date('Y-m-t'),
            'client_id' => $request->input('client_id'),
            'vehicule_id' => $request->input('vehicule_id'),
            'remorque_id' => $request->input('remorque_id'),
            'chauffeur_id' => $request->input('chauffeur_id'),
            'user_id' => $request->input('user_id'),
        ];
        $vehiculeFilters = array_filter($vehiculeFilters);

        return array_merge($baseFilters, $vehiculeFilters);
    }
}
