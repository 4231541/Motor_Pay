<?php

namespace App\Enums;

enum CarStatus: string
{
    case Available = 'available';
    case Reserved  = 'reserved';
    case Sold      = 'sold';
    case Inactive  = 'inactive';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Reserved  => 'Reserved',
            self::Sold      => 'Sold',
            self::Inactive  => 'Inactive',
        };
    }

    /**
     * Returns all values as a plain array (useful for validation rules).
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
