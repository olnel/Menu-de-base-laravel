<?php

namespace App\Utils;
use Illuminate\Http\Request;

class ExtractFiltreFactureClient extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        $vehiculeFilters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'etat_facture' => $request->input('etat_facture'),
            'client_id' => $request->input('client_id'),
            'user_id' => $request->input('user_id'),
            'statut_facture' => $request->input('statut_facture'),
        ];
        $vehiculeFilters = array_filter($vehiculeFilters);

        return array_merge($baseFilters, $vehiculeFilters);

    }
}
