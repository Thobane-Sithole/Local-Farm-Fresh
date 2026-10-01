<?php

namespace Tests\Feature\Farmer;

use App\Enums\ProductUnit;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $farmer;
    private FarmerProfile $profile;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->farmer = User::factory()->create();
        $this->farmer->role = UserRole::Farmer;
        $this->farmer->save();
        $this->profile = FarmerProfile::factory()->for($this->farmer)->create();
        $this->category = Category::factory()->create(['is_active' => true]);

        // Prevent actual Cloudinary calls in tests.
        $this->mock(CloudinaryService::class, function ($mock) {
            $mock->shouldReceive('uploadProductImage')->andReturn([
                'url' => 'https://res.cloudinary.com/demo/image/upload/sample.jpg',
                'public_id' => 'local-farm-fresh/products/sample',
            ]);
            $mock->shouldReceive('delete')->andReturn(null);
        });
    }

    public function test_farmer_can_view_product_index(): void
    {
        $this->actingAs($this->farmer)
            ->get(route('farmer.products.index'))
            ->assertOk();
    }

    public function test_farmer_can_view_create_form(): void
    {
        $this->actingAs($this->farmer)
            ->get(route('farmer.products.create'))
            ->assertOk()
            ->assertSee('Add a product');
    }

    public function test_farmer_can_create_product(): void
    {
        $this->actingAs($this->farmer)
            ->post(route('farmer.products.store'), [
                'category_id' => $this->category->id,
                'name' => 'Fresh Tomatoes',
                'price' => '18.00',
                'unit' => ProductUnit::Kilogram->value,
                'quantity_available' => 50,
                'is_available' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'farmer_profile_id' => $this->profile->id,
            'name' => 'Fresh Tomatoes',
        ]);
    }

    public function test_farmer_can_update_own_product(): void
    {
        $product = Product::factory()
            ->for($this->profile)
            ->for($this->category)
            ->create();

        $this->actingAs($this->farmer)
            ->put(route('farmer.products.update', $product), [
                'category_id' => $this->category->id,
                'name' => 'Organic Tomatoes',
                'price' => '22.00',
                'unit' => ProductUnit::Kilogram->value,
                'quantity_available' => 30,
                'is_available' => true,
            ])
            ->assertRedirect();

        $this->assertEquals('Organic Tomatoes', $product->fresh()->name);
    }

    public function test_farmer_cannot_edit_another_farmers_product(): void
    {
        $otherFarmer = User::factory()->create();
        $otherFarmer->role = UserRole::Farmer;
        $otherFarmer->save();
        $otherProfile = FarmerProfile::factory()->for($otherFarmer)->create();
        $product = Product::factory()->for($otherProfile)->for($this->category)->create();

        $this->actingAs($this->farmer)
            ->get(route('farmer.products.edit', $product))
            ->assertForbidden();
    }

    public function test_farmer_can_delete_own_product(): void
    {
        $product = Product::factory()
            ->for($this->profile)
            ->for($this->category)
            ->create();

        $this->actingAs($this->farmer)
            ->delete(route('farmer.products.destroy', $product))
            ->assertRedirect(route('farmer.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_product_name_is_required(): void
    {
        $this->actingAs($this->farmer)
            ->post(route('farmer.products.store'), [
                'category_id' => $this->category->id,
                'name' => '',
                'price' => '10.00',
                'unit' => ProductUnit::Kilogram->value,
                'quantity_available' => 5,
            ])
            ->assertSessionHasErrors('name');
    }
}
