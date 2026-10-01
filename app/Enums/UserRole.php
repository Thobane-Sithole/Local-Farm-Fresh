<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Farmer = 'farmer';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Farmer => 'Farmer',
            self::Admin => 'Admin',
        };
    }

    /** Named route each role lands on after signing in. */
    public function homeRoute(): string
    {
        return match ($this) {
            self::Customer => 'account.index',
            self::Farmer => 'farmer.dashboard',
            self::Admin => 'admin.dashboard',
        };
    }
}
