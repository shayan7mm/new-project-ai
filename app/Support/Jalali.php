<?php

namespace App\Support;

use DateTimeInterface;
use IntlDateFormatter;

final class Jalali
{
    /** تاریخ و ساعت شمسی با ارقام فارسی، مثل «۱۴۰۵/۰۸/۲۷ — ۱۴:۳۰» */
    public static function dateTime(DateTimeInterface $date): string
    {
        $formatter = new IntlDateFormatter(
            'fa_IR@calendar=persian',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            config('app.timezone'),
            IntlDateFormatter::TRADITIONAL,
            'yyyy/MM/dd — HH:mm'
        );

        return (string) $formatter->format($date);
    }

    /** سال شمسی (عدد لاتین) */
    public static function year(DateTimeInterface $date): string
    {
        $formatter = new IntlDateFormatter(
            'en_US@calendar=persian',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            config('app.timezone'),
            IntlDateFormatter::TRADITIONAL,
            'yyyy'
        );

        return (string) $formatter->format($date);
    }
}
