<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\NewOrderFarmer;
use App\Notifications\OrderPlacedCustomer;

class SendOrderPlacedNotifications
{
    public function handle(OrderPlaced $event): void
    {
        // Eager-load related data on each order model individually
        foreach ($event->orders as $order) {
            $order->loadMissing('items', 'farmerProfile.user', 'customer');
        }

        // Confirm to the customer (one notification covers all orders in the group)
        $event->customer->notify(new OrderPlacedCustomer($event->orders));

        // Notify each farmer individually
        foreach ($event->orders as $order) {
            $farmerUser = $order->farmerProfile?->user;
            if ($farmerUser) {
                $farmerUser->notify(new NewOrderFarmer($order));
            }
        }
    }
}
