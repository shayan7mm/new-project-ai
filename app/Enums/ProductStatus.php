<?php

namespace App\Enums;

enum ProductStatus: string
{
    case InStock = 'in';
    case Low = 'low';
    case OutOfStock = 'out';

    public function label(): string
    {
        return match ($this) {
            self::InStock => 'موجود',
            self::Low => 'کم',
            self::OutOfStock => 'ناموجود',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::InStock => 'var(--success)',
            self::Low => 'var(--warn)',
            self::OutOfStock => 'var(--danger)',
        };
    }

    /** @return array<string,string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
