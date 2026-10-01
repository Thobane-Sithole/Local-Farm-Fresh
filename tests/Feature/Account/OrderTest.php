<?php

namespace Tests\Feature\Account;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_account_orders(): void
    {
        $this->get(route('account.orders.index'))->assertRedirect(route('login'));
    }

    public function test_customer_can_view_their_orders(): void
    {
        $customer = User::factory()->create();
        $order    = Order::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($customer)
            ->get(route('account.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_customer_cannot_see_other_customers_orders(): void
    {
        $customerA = User::factory()->create();
        $customerB = User::factory()->create();
        $orderB    = Order::factory()->create(['customer_id' => $customerB->id]);

        $response = $this->actingAs($customerA)
            ->get(route('account.orders.index'));

        $response->assertOk()->assertDontSee($orderB->order_number);
    }

    public function test_customer_can_view_their_own_order_detail(): void
    {
        $customer = User::factory()->create();
        $order    = Order::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($customer)
            ->get(route('account.orders.show', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_customer_cannot_view_another_customers_order_detail(): void
    {
        $customerA = User::factory()->create();
        $customerB = User::factory()->create();
        $orderB    = Order::factory()->create(['customer_id' => $customerB->id]);

        $this->actingAs($customerA)
            ->get(route('account.orders.show', $orderB->order_number))
            ->assertForbidden();
    }
}
