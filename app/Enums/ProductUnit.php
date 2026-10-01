<?php

namespace App\Enums;

enum ProductUnit: string
{
    case Kilogram = 'kg';
    case Gram = 'g';
    case Bunch = 'bunch';
    case Dozen = 'dozen';
    case Litre = 'litre';
    case Bottle = 'bottle';
    case Basket = 'basket';
    case Bag = 'bag';
    case Item = 'item';

    public function label(): string
    {
        return match ($this) {
            self::Kilogram => 'kg',
            self::Gram => 'g',
            self::Litre => 'litre',
            default => $this->value,
        };
    }
}
