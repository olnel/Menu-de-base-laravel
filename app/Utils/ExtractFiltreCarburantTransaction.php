<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltreCarburantTransaction extends ExtractFiltre
{
    public static function extractFilter(Request $request)
    {

        $baseFilters = parent::extractFilter($request);
        $filter = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'user_id' => $request->input('user_id'),
            'chauffeur_id'=>$request->input('chauffeur_id'),
            'vehicule_id'=>$request->input('vehicule_id'),
            'carburant_card_id'=>$request->input('carburant_card_id'),
            'type_mvmt'=>$request->input('type_mvmt'),
        ];
        $filtre = array_filter($filter);

        return array_merge($baseFilters, $filtre);
    }
}
