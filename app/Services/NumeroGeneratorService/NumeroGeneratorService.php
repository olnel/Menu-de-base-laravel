<?php

namespace App\Services\NumeroGeneratorService;

use Carbon\Carbon;

class NumeroGeneratorService
{
    public function generate( callable $getLastCountCallback, string $prefix = 'NUM', string $suffixFormat = 'm-Y', int $padding = 3): array {
        $first_date = Carbon::now()->startOfMonth()->toDateString();
        $last_date = Carbon::now()->endOfMonth()->toDateString();

        $last = $getLastCountCallback($first_date, $last_date);

        $count = $last ? $last + 1 : 1;
        $suffix = Carbon::now()->format($suffixFormat);
        $numero = sprintf('%s-%0' . $padding . 'd/%s', $prefix, $count, $suffix);

        return [
            'count' => $count,
            'numero' => $numero
        ];
    }
}
