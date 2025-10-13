<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';
    case KUNDEN = 'kunden';
    case MARKETING = 'marketing';
    case LAGER = 'lager';
    case VERTRIEB = 'vertrieb';

    /**
     * Provide a human readable label for UI rendering.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::EMPLOYEE => 'Employee',
            self::KUNDEN => 'Kunden',
            self::MARKETING => 'Marketing',
            self::LAGER => 'Lager',
            self::VERTRIEB => 'Vertrieb',
        };
    }

    /**
     * Determine if the current value represents a management-level role.
     */
    public function isManager(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Roles that naturally supervise all departments.
     *
     * @return array<int, self>
     */
    public static function supervisors(): array
    {
        return [self::ADMIN];
    }
}

