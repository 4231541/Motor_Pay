<?php

namespace App\Enums;

enum RequestType: string
{
    case Booking     = 'booking';
    case Installment = 'installment';

    public function label(): string
    {
        return match ($this) {
            self::Booking     => 'Booking',
            self::Installment => 'Installment Financing',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
