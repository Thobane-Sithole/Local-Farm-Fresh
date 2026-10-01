<?php

namespace Tests\Feature\Authorization;

use App\Enums\OrderStatus;
use App\Models\FarmerProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmers_can_only_manage_their_own_products(): void
    {
        $mine = Product::factory()->create();
        $theirs = Product::factory()->create();
        $farmer = $mine->farmerProfile->user;

        $this->assertTrue($farmer->can('update', $mine));
        $this->assertTrue($farmer->can('delete', $mine));
        $this->assertFalse($farmer->can('update', $theirs));
        $this->assertFalse($farmer->can('delete', $theirs));
    }

    public function test_customers_cannot_manage_products(): void
    {
        $product = Product::factory()->create();
        $customer = User::factory()->create();

        $this->assertFalse($customer->can('create', Product::class));
        $this->assertFalse($customer->can('update', $product));
    }

    public function test_admins_can_manage_any_product(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->can('update', Product::factory()->create()));
    }

    public function test_orders_are_visible_only_to_their_customer_and_farmer(): void
    {
        $order = Order::factory()->create();
        $customer = $order->customer;
        $farmer = $order->farmerProfile->user;
        $otherCustomer = User::factory()->create();
        $otherFarmer = FarmerProfile::factory()->create()->user;

        $this->assertTrue($customer->can('view', $order));
        $this->assertTrue($farmer->can('view', $order));
        $this->assertFalse($otherCustomer->can('view', $order));
        $this->assertFalse($otherFarmer->can('view', $order));
    }

    public function test_only_the_fulfilling_farmer_updates_status(): void
    {
        $order = Order::factory()->create();

        $this->assertTrue($order->farmerProfile->user->can('updateStatus', $order));
        $this->assertFalse($order->customer->can('updateStatus', $order));
    }

    public function test_customer_can_cancel_only_while_pending(): void
    {
        $order = Order::factory()->create();
        $this->assertTrue($order->customer->can('cancel', $order));

        $order->forceFill(['status' => OrderStatus::Confirmed])->save();
        $this->assertFalse($order->customer->fresh()->can('cancel', $order->fresh()));
    }
}
