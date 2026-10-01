<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->role = UserRole::Admin;
        $this->admin->save();
    }

    public function test_admin_can_view_categories(): void
    {
        Category::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Categories');
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Root Vegetables',
                'description' => 'Potatoes, carrots, beetroot and more.',
                'sort_order' => 5,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Root Vegetables']);
    }

    public function test_category_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Leafy Greens']);

        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Leafy Greens',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Vegetables']);

        $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Fresh Vegetables',
                'sort_order' => 1,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertEquals('Fresh Vegetables', $category->fresh()->name);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $category = Category::factory()->create();
        $farmer = User::factory()->create();
        $farmer->role = UserRole::Farmer;
        $farmer->save();
        $profile = FarmerProfile::factory()->for($farmer)->create();
        Product::factory()->for($profile)->for($category)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_customer_cannot_access_category_management(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }
}
