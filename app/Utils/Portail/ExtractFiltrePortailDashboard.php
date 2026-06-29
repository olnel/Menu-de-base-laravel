<?php

namespace App\Utils\Portail;

use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;

class ExtractFiltrePortailDashboard extends ExtractFiltre
{
    public static function extractFilter(Request $request): array
    {
        return array_filter([
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ]);
    }
}
