<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_development_seed_builds_a_realistic_marketplace(): void
    {
        $this->seed();

        $this->assertSame(10, Category::count());
        $this->assertSame(10, FarmerProfile::count());
        $this->assertGreaterThanOrEqual(30, Product::count());
        $this->assertSame(20, User::role(UserRole::Customer)->count());
        $this->assertSame(1, User::role(UserRole::Admin)->count());
        $this->assertSame(Product::count(), Product::visible()->count());
        $this->assertNotNull(Product::where('slug', 'fresh-tomatoes')->first());
    }
}
