<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OrderPlaced
{
    use Dispatchable, SerializesModels;

    /**
     * @param Collection $orders  The newly-created orders (one per farmer)
     * @param User       $customer The customer who placed them
     */
    public function __construct(
        public readonly Collection $orders,
        public readonly User       $customer,
    ) {}
}
