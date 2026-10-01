<?php

namespace App\Events;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusAdvanced
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order       $order,
        public readonly OrderStatus $newStatus,
        public readonly User        $actor,
    ) {}
}
