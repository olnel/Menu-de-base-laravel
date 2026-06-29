<?php

namespace App\Utils;

use Carbon\Carbon;

class ForamatDate
{
    public static function normaliserDate($dateString)
    {
        // Si la date est déjà au format Y-m-d, la retourner directement
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        $formats = ['d/m/y', 'd-m-Y', 'Y-m-d', 'd/m/Y', 'm/d/Y', 'm-d-Y'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date && $date->format($format) === $dateString) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return false;
    }
}
