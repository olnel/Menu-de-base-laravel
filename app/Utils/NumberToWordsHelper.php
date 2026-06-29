<?php

namespace App\Utils;

class NumberToWordsHelper
{
    private static $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
    private static $dizaines = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
    
    public static function convert($number)
    {
        $number = (int)floor($number);
        
        if ($number === 0) {
            return 'zéro';
        }

        if ($number < 0) {
            return 'moins ' . self::convert(abs($number));
        }

        $result = '';

        // Millions
        if ($number >= 1000000) {
            $millions = (int)($number / 1000000);
            $result .= ($millions > 1 ? self::convert($millions) . ' ' : 'un ') . 'million' . ($millions > 1 ? 's' : '') . ' ';
            $number %= 1000000;
        }

        // Milliers
        if ($number >= 1000) {
            $mille = (int)($number / 1000);
            $result .= ($mille > 1 ? self::convert($mille) . ' ' : '') . 'mille ';
            $number %= 1000;
        }

        // Cents
        if ($number >= 100) {
            $cent = (int)($number / 100);
            $result .= ($cent > 1 ? self::$unites[$cent] . ' ' : '') . 'cent' . ($cent > 1 && $number % 100 == 0 ? 's' : '') . ' ';
            $number %= 100;
        }

        // Reste (0-99)
        if ($number > 0) {
            if ($number < 10) {
                $result .= self::$unites[$number];
            } elseif ($number < 20) {
                $exceptions = [
                    10 => 'dix', 11 => 'onze', 12 => 'douze', 13 => 'treize',
                    14 => 'quatorze', 15 => 'quinze', 16 => 'seize',
                    17 => 'dix-sept', 18 => 'dix-huit', 19 => 'dix-neuf'
                ];
                $result .= $exceptions[$number];
            } else {
                $dizaine = (int)($number / 10);
                $unite = $number % 10;

                if ($dizaine == 7) {
                    $result .= 'soixante-' . ($unite == 1 ? 'et-onze' : self::convert(10 + $unite));
                } elseif ($dizaine == 9) {
                    $result .= 'quatre-vingt-' . self::convert(10 + $unite);
                } else {
                    $result .= self::$dizaines[$dizaine];
                    if ($unite > 0) {
                        $result .= ($unite == 1 && $dizaine != 8 ? '-et-' : '-') . self::$unites[$unite];
                    }
                }
            }
        }

        return trim($result);
    }
}
