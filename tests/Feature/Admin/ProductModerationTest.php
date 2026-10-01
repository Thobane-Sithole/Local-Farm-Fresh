<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_products_list(): void
    {
        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_non_admin_cannot_access_admin_products_page(): void
    {
        $farmer = User::factory()->farmer()->create();

        $this->actingAs($farmer)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_remove_a_product(): void
    {
        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.products.remove', $product))
            ->assertRedirect();

        $this->assertNotNull($product->fresh()->removed_at);
    }

    public function test_admin_can_restore_a_removed_product(): void
    {
        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create(['removed_at' => now()]);

        $this->actingAs($admin)
            ->post(route('admin.products.restore', $product))
            ->assertRedirect();

        $this->assertNull($product->fresh()->removed_at);
    }

    public function test_removed_product_does_not_appear_in_shop(): void
    {
        $product = Product::factory()->create(['removed_at' => now()]);

        $this->get(route('shop.index'))
            ->assertDontSee($product->name);
    }

    public function test_active_filter_hides_removed_products(): void
    {
        $admin   = User::factory()->admin()->create();
        $removed = Product::factory()->create(['removed_at' => now()]);
        $active  = Product::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.products.index', ['status' => 'active']))
            ->assertOk()
            ->assertSee($active->name)
            ->assertDontSee($removed->name);
    }

    public function test_removed_filter_shows_only_removed_products(): void
    {
        $admin   = User::factory()->admin()->create();
        $removed = Product::factory()->create(['removed_at' => now()]);
        $active  = Product::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.products.index', ['status' => 'removed']))
            ->assertOk()
            ->assertSee($removed->name)
            ->assertDontSee($active->name);
    }
}
