<?php

namespace App\Enums;

enum CategoryType: string
{
    case Equipment = 'equipment';
    case Consumable = 'consumable';

    public function label(): string
    {
        return match ($this) {
            self::Equipment => 'تجهیزات',
            self::Consumable => 'مواد مصرفی',
        };
    }

    /** @return array<string,string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
