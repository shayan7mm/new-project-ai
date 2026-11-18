<?php

namespace App\Support;

use DateTimeInterface;

class Jalali
{
    /** تبدیل تاریخ میلادی به شمسی: [سال, ماه, روز] */
    public static function toJalali(int $gy, int $gm, int $gd): array
    {
        $g_d_m = [0,31,59,90,120,151,181,212,243,273,304,334];
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4))
              - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400))
              + $gd + $g_d_m[$gm - 1];

        $jy = -1595 + (33 * ((int)($days / 12053)));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;

        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }

        if ($days < 186) {
            $jm = 1 + (int)($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + (int)(($days - 186) / 30);
            $jd = 1 + (($days - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }

    /** سال شمسی */
    public static function year(DateTimeInterface $date): string
    {
        [$jy] = self::toJalali((int)$date->format('Y'), (int)$date->format('n'), (int)$date->format('j'));
        return (string) $jy;
    }

    /** ماه شمسی */
    public static function month(DateTimeInterface $date): string
    {
        [, $jm] = self::toJalali((int)$date->format('Y'), (int)$date->format('n'), (int)$date->format('j'));
        return (string) $jm;
    }

    /** روز شمسی */
    public static function day(DateTimeInterface $date): string
    {
        [, , $jd] = self::toJalali((int)$date->format('Y'), (int)$date->format('n'), (int)$date->format('j'));
        return (string) $jd;
    }

    /** تاریخ کامل شمسی: YYYY/MM/DD */
    public static function date(DateTimeInterface $date): string
    {
        [$jy, $jm, $jd] = self::toJalali((int)$date->format('Y'), (int)$date->format('n'), (int)$date->format('j'));
        return sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
    }

    /** تاریخ و ساعت شمسی: YYYY/MM/DD HH:MM:SS */
    public static function dateTime(DateTimeInterface $date): string
    {
        return self::date($date) . ' ' . $date->format('H:i:s');
    }
}