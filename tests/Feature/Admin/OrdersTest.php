<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_orders(): void
    {
        $admin    = User::factory()->admin()->create();
        $orderA   = Order::factory()->create(['customer_id' => User::factory()]);
        $orderB   = Order::factory()->create(['customer_id' => User::factory()]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($orderA->order_number)
            ->assertSee($orderB->order_number);
    }

    public function test_non_admin_cannot_access_admin_orders(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.orders.index'))
            ->assertForbidden();
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        $admin   = User::factory()->admin()->create();
        $pending = Order::factory()->create([
            'customer_id' => User::factory(),
            'status'      => OrderStatus::Pending,
        ]);
        $delivered = Order::factory()->create([
            'customer_id' => User::factory(),
            'status'      => OrderStatus::Delivered,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index', ['status' => OrderStatus::Pending->value]))
            ->assertOk()
            ->assertSee($pending->order_number)
            ->assertDontSee($delivered->order_number);
    }

    public function test_admin_can_view_individual_order(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['customer_id' => User::factory()]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_admin_order_search_finds_by_order_number(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['customer_id' => User::factory()]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index', ['q' => $order->order_number]))
            ->assertOk()
            ->assertSee($order->order_number);
    }
}
