<?php

namespace Tests\Feature\Farmer;

use App\Enums\OrderStatus;
use App\Models\FarmerProfile;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_view_their_orders(): void
    {
        $profile = FarmerProfile::factory()->create();
        Order::factory()->create(['farmer_profile_id' => $profile->id, 'customer_id' => User::factory()]);

        $this->actingAs($profile->user)
            ->get(route('farmer.orders.index'))
            ->assertOk()
            ->assertViewIs('farmer.orders.index');
    }

    public function test_farmer_cannot_view_other_farmers_orders(): void
    {
        $profileA = FarmerProfile::factory()->create();
        $profileB = FarmerProfile::factory()->create();
        $order    = Order::factory()->create([
            'farmer_profile_id' => $profileB->id,
            'customer_id'       => User::factory(),
        ]);

        $this->actingAs($profileA->user)
            ->get(route('farmer.orders.show', $order->order_number))
            ->assertForbidden();
    }

    public function test_farmer_can_view_their_own_order(): void
    {
        $profile = FarmerProfile::factory()->create();
        $order   = Order::factory()->create([
            'farmer_profile_id' => $profile->id,
            'customer_id'       => User::factory(),
        ]);

        $this->actingAs($profile->user)
            ->get(route('farmer.orders.show', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_farmer_can_advance_order_status(): void
    {
        $profile = FarmerProfile::factory()->create();
        $order   = Order::factory()->create([
            'farmer_profile_id' => $profile->id,
            'customer_id'       => User::factory(),
            'status'            => OrderStatus::Pending,
        ]);

        $this->actingAs($profile->user)
            ->patch(route('farmer.orders.status', $order->order_number), [
                'status' => OrderStatus::Confirmed->value,
            ])
            ->assertRedirect();

        $this->assertEquals(OrderStatus::Confirmed, $order->fresh()->status);
    }

    public function test_farmer_cannot_update_other_farmers_order_status(): void
    {
        $profileA = FarmerProfile::factory()->create();
        $profileB = FarmerProfile::factory()->create();
        $order    = Order::factory()->create([
            'farmer_profile_id' => $profileB->id,
            'customer_id'       => User::factory(),
            'status'            => OrderStatus::Pending,
        ]);

        $this->actingAs($profileA->user)
            ->patch(route('farmer.orders.status', $order->order_number), [
                'status' => OrderStatus::Confirmed->value,
            ])
            ->assertForbidden();
    }

    public function test_non_farmer_cannot_access_farmer_orders(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('farmer.orders.index'))
            ->assertForbidden();
    }
}
