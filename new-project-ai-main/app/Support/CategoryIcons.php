<?php

namespace App\Support;

/**
 * لیست سفید آیکون‌های مجاز دسته‌ها (Font Awesome).
 * فقط همین مقادیر در فرم پذیرفته می‌شوند تا ورودی دلخواه وارد کلاس HTML نشود.
 */
final class CategoryIcons
{
    public const ALL = [
        'fa-tooth' => '🦷 دندان',
        'fa-chair' => '🪑 صندلی',
        'fa-x-ray' => '📷 رادیولوژی',
        'fa-screwdriver-wrench' => '🔧 ابزار',
        'fa-syringe' => '💉 سرنگ',
        'fa-microchip' => '💾 چیپ',
        'fa-toolbox' => '🧰 هندپیس',
        'fa-box' => '📦 جعبه',
        'fa-microscope' => '🔬 میکروسکوپ',
        'fa-stethoscope' => '🩺 پزشکی',
        'fa-shield-virus' => '🛡️ استریل',
        'fa-wand-magic-sparkles' => '✨ دیجیتال',
        'fa-bolt' => '⚡ کیور',
        'fa-flask' => '⚗️ آزمایشگاه',
        'fa-link' => '🔗 ارتودنسی',
    ];

    public static function keys(): array
    {
        return array_keys(self::ALL);
    }
}
