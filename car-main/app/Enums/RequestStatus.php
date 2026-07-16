<?php

namespace App\Enums;

enum RequestStatus: string
{
    case Received   = 'received';
    case Reviewing  = 'reviewing';
    case Contacting = 'contacting';
    case Approved   = 'approved';
    case Rejected   = 'rejected';
    case Delivered  = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::Received   => 'Received',
            self::Reviewing  => 'Under Review',
            self::Contacting => 'Contacting Customer',
            self::Approved   => 'Approved',
            self::Rejected   => 'Rejected',
            self::Delivered  => 'Delivered',
        };
    }

    /**
     * Returns valid next statuses from a given status to enforce workflow transitions.
     *
     * @return RequestStatus[]
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Received   => [self::Reviewing, self::Rejected],
            self::Reviewing  => [self::Contacting, self::Rejected],
            self::Contacting => [self::Approved, self::Rejected],
            self::Approved   => [self::Delivered],
            self::Rejected   => [],
            self::Delivered  => [],
        };
    }

    /**
     * Check whether a transition to another status is valid.
     */
    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
