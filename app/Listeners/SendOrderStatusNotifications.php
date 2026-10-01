<?php

namespace App\Listeners;

use App\Events\OrderStatusAdvanced;
use App\Notifications\OrderStatusChanged;

class SendOrderStatusNotifications
{
    public function handle(OrderStatusAdvanced $event): void
    {
        $event->order->load('farmerProfile');

        $customer = $event->order->customer;
        if ($customer) {
            $customer->notify(new OrderStatusChanged($event->order, $event->newStatus));
        }
    }
}
