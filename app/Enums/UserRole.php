<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case CASHIER = 'cashier';
    case HR = 'hr';

    /**
     * Get all role values
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get all roles as choices for select dropdowns
     */
    public static function choices(): array
    {
        return array_combine(
            self::values(),
            array_map(fn($case) => ucfirst($case->value), self::cases())
        );
    }
}
