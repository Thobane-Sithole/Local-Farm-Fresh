<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

/** Admins are allowed everything via Gate::before in AppServiceProvider. */
class OrderPolicy
{
    /** The customer who placed it, or the farmer fulfilling it. */
    public function view(User $user, Order $order): bool
    {
        return $this->isCustomer($user, $order) || $this->isFarmer($user, $order);
    }

    /** Only the fulfilling farmer moves an order through its lifecycle. */
    public function updateStatus(User $user, Order $order): bool
    {
        return $this->isFarmer($user, $order) && $order->status->isOpen();
    }

    /** Customers may cancel only before the farmer has confirmed. */
    public function cancel(User $user, Order $order): bool
    {
        return $this->isCustomer($user, $order) && $order->status === \App\Enums\OrderStatus::Pending;
    }

    private function isCustomer(User $user, Order $order): bool
    {
        return $order->customer_id === $user->id;
    }

    private function isFarmer(User $user, Order $order): bool
    {
        return $user->isFarmer()
            && $user->farmerProfile !== null
            && $order->farmer_profile_id === $user->farmerProfile->id;
    }
}
