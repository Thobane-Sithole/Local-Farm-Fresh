<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user, Order $order): bool
    {
        return $order->customer_id === $user->id
            && $order->status === OrderStatus::Delivered
            && $order->review === null;
    }
}
