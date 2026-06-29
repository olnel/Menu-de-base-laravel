<?php

namespace App\Utils;
use Illuminate\Http\Request;
class ExtractFiltreVoyage extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        $vehiculeFilters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'etat_facture' => $request->input('etat_facture'),
            'client_id' => $request->input('client_id'),
            'vehicule_id' => $request->input('vehicule_id'),
            'user_id' => $request->input('user_id'),
            'chauffeur_id' => $request->input('chauffeur_id'),
            'statut' => $request->input('statut'),
        ];
        $vehiculeFilters = array_filter($vehiculeFilters);

        return array_merge($baseFilters, $vehiculeFilters);

    }
}
