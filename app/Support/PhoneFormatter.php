<?php

namespace App\Support;

class PhoneFormatter
{
    /** Маска ввода: +7 (999) 123-45-67 */
    public static function format(?string $value): string
    {
        $digits = self::digits($value);

        if ($digits === '') {
            return '';
        }

        if (! str_starts_with($digits, '7')) {
            $digits = '7'.$digits;
        }

        $digits = substr($digits, 0, 11);
        $local = substr($digits, 1);

        $part1 = substr($local, 0, 3);
        $part2 = substr($local, 3, 3);
        $part3 = substr($local, 6, 2);
        $part4 = substr($local, 8, 2);

        $formatted = '+7';

        if ($part1 !== '') {
            $formatted .= ' ('.$part1;
            if (strlen($part1) === 3) {
                $formatted .= ')';
            }
        }

        if ($part2 !== '') {
            $formatted .= (strlen($part1) === 3 ? ' ' : '').$part2;
        }

        if ($part3 !== '') {
            $formatted .= '-'.$part3;
        }

        if ($part4 !== '') {
            $formatted .= '-'.$part4;
        }

        return $formatted;
    }

    public static function normalize(?string $value): ?string
    {
        $formatted = self::format($value);

        return $formatted === '' ? null : $formatted;
    }

    public static function digits(?string $value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '8') && strlen($digits) >= 11) {
            $digits = '7'.substr($digits, 1);
        }

        if (! str_starts_with($digits, '7') && strlen($digits) === 10) {
            $digits = '7'.$digits;
        }

        return substr($digits, 0, 11);
    }
}
