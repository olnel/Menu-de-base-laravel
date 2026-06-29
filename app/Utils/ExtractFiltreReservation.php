<?php

namespace App\Utils;
use Illuminate\Http\Request;
class ExtractFiltreReservation extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        $vehiculeFilters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'client_id' => $request->input('client_id'),
            'user_id' => $request->input('user_id'),
        ];
        $vehiculeFilters = array_filter($vehiculeFilters);

        return array_merge($baseFilters, $vehiculeFilters);

    }
}
