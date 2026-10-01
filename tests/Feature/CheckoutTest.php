<?php

namespace Tests\Feature;

use App\Enums\Province;
use App\Models\Cart;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function validAddress(): array
    {
        return [
            'recipient_name' => 'Test Customer',
            'phone'          => '0721234567',
            'street_address' => '15 Bree Street',
            'town'           => 'Cape Town',
            'province'       => Province::WesternCape->value,
        ];
    }

    private function cartWithProduct(User $user, Product $product, int $qty = 1): Cart
    {
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => $qty]);

        return $cart;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('checkout.index'))->assertRedirect(route('login'));
        $this->post(route('checkout.store'), $this->validAddress())->assertRedirect(route('login'));
    }

    public function test_empty_cart_redirects_to_cart_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'));
    }

    public function test_checkout_page_shows_cart_summary(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        $this->cartWithProduct($user, $product);

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_successful_checkout_creates_order_and_empties_cart(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['quantity_available' => 10]);
        $this->cartWithProduct($user, $product, 2);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->validAddress())
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'customer_id'       => $user->id,
            'farmer_profile_id' => $product->farmerProfile->id,
        ]);

        $this->assertDatabaseEmpty('cart_items');
    }

    public function test_checkout_reduces_product_stock(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['quantity_available' => 10]);
        $this->cartWithProduct($user, $product, 3);

        $this->actingAs($user)->post(route('checkout.store'), $this->validAddress());

        $this->assertEquals(7, $product->fresh()->quantity_available);
    }

    public function test_cart_from_two_farmers_creates_two_orders(): void
    {
        $user     = User::factory()->create();
        $productA = Product::factory()->create(['quantity_available' => 5]);
        $productB = Product::factory()->create(['quantity_available' => 5]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $productA->id, 'quantity' => 1]);
        $cart->items()->create(['product_id' => $productB->id, 'quantity' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), $this->validAddress());

        $this->assertDatabaseCount('orders', 2);
    }

    public function test_checkout_requires_recipient_name(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['quantity_available' => 5]);
        $this->cartWithProduct($user, $product);

        $data = $this->validAddress();
        unset($data['recipient_name']);

        $this->actingAs($user)
            ->post(route('checkout.store'), $data)
            ->assertSessionHasErrors('recipient_name');
    }

    public function test_checkout_requires_valid_province(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['quantity_available' => 5]);
        $this->cartWithProduct($user, $product);

        $this->actingAs($user)
            ->post(route('checkout.store'), array_merge($this->validAddress(), ['province' => 'not_a_province']))
            ->assertSessionHasErrors('province');
    }

    public function test_confirmed_page_shows_order_summary(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['quantity_available' => 5]);
        $this->cartWithProduct($user, $product);

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), $this->validAddress());

        $order = $user->orders()->first();
        $response->assertRedirect(route('checkout.confirmed', ['group' => $order->checkout_group]));

        $this->actingAs($user)
            ->get(route('checkout.confirmed', ['group' => $order->checkout_group]))
            ->assertOk()
            ->assertSee($order->order_number);
    }
}
