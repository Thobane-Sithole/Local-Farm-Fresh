<?php

namespace App\Enums;

/**
 * Version 1 supports cash on delivery only.
 * New gateways are added here later without touching the orders schema.
 */
enum PaymentMethod: string
{
    case CashOnDelivery = 'cash_on_delivery';

    public function label(): string
    {
        return match ($this) {
            self::CashOnDelivery => 'Cash on Delivery',
        };
    }
}
