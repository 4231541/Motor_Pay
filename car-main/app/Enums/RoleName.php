<?php

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case SalesManager = 'sales_manager';
    case SalesAgent = 'sales_agent';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::SalesManager => 'Sales Manager',
            self::SalesAgent => 'Sales Agent',
            self::Customer => 'Customer',
        };
    }
}
