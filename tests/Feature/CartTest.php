<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_product_to_cart(): void
    {
        $product = Product::factory()->create();

        $this->post(route('cart.store'), ['product_id' => $product->id])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
    }

    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)
            ->post(route('cart.store'), ['product_id' => $product->id])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 1]);
    }

    public function test_adding_same_product_again_increments_quantity(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        $cart    = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user)
            ->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 3])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 5]);
    }

    public function test_user_can_update_item_quantity(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        $cart    = Cart::create(['user_id' => $user->id]);
        $item    = $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->patch(route('cart.update', $item), ['quantity' => 5])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 5]);
    }

    public function test_setting_quantity_to_zero_removes_item(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        $cart    = Cart::create(['user_id' => $user->id]);
        $item    = $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user)
            ->patch(route('cart.update', $item), ['quantity' => 0])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create();
        $cart    = Cart::create(['user_id' => $user->id]);
        $item    = $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->delete(route('cart.destroy', $item))
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_unavailable_product_cannot_be_added(): void
    {
        $product = Product::factory()->unavailable()->create();

        $this->post(route('cart.store'), ['product_id' => $product->id])
            ->assertStatus(422);
    }

    public function test_removed_product_cannot_be_added(): void
    {
        $product = Product::factory()->create(['removed_at' => now()]);

        $this->post(route('cart.store'), ['product_id' => $product->id])
            ->assertStatus(422);
    }

    public function test_cart_page_is_accessible_to_guests(): void
    {
        $this->get(route('cart.index'))->assertOk();
    }
}
