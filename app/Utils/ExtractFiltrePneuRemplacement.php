<?php

namespace App\Utils;

use Illuminate\Http\Request;

class ExtractFiltrePneuRemplacement extends ExtractFiltre
{
    public static function extractFilter(Request $request): array
    {
        $baseFilters = parent::extractFilter($request);

        $filters = [
            'start_date'     => $request->input('start_date'),
            'end_date'       => $request->input('end_date'),
            'vehicule_id'    => $request->input('vehicule_id'),
            'type_operation' => $request->input('type_operation'),
        ];

        return array_merge($baseFilters, array_filter($filters, fn($v) => $v !== null && $v !== ''));
    }
}
