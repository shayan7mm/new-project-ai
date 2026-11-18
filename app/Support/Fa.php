<?php

namespace App\Support;

final class Fa
{
    public static function digits(string|int|null $value): string
    {
        return strtr((string) $value, ['0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹']);
    }

    public static function price(int|null $value): string
    {
        return self::digits(number_format((int) $value));
    }
}
