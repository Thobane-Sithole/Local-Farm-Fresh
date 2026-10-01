<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_lifecycle_moves_forward_one_step_at_a_time(): void
    {
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Confirmed));
        $this->assertTrue(OrderStatus::Confirmed->canTransitionTo(OrderStatus::Preparing));
        $this->assertTrue(OrderStatus::OutForDelivery->canTransitionTo(OrderStatus::Delivered));

        $this->assertFalse(OrderStatus::Pending->canTransitionTo(OrderStatus::Delivered));
        $this->assertFalse(OrderStatus::Preparing->canTransitionTo(OrderStatus::Pending));
    }

    public function test_finished_orders_cannot_change(): void
    {
        $this->assertSame([], OrderStatus::Delivered->allowedTransitions());
        $this->assertSame([], OrderStatus::Cancelled->allowedTransitions());
        $this->assertFalse(OrderStatus::Delivered->isOpen());
    }

    public function test_out_for_delivery_orders_cannot_be_cancelled(): void
    {
        $this->assertFalse(OrderStatus::OutForDelivery->canTransitionTo(OrderStatus::Cancelled));
    }
}
