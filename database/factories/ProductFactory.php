<?php

namespace Database\Factories;

use App\Enums\ProductUnit;
use App\Models\Category;
use App\Models\FarmerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'farmer_profile_id' => FarmerProfile::factory(),
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'short_description' => 'Freshly harvested this week.',
            'price' => fake()->randomFloat(2, 8, 200),
            'unit' => fake()->randomElement(ProductUnit::cases()),
            'quantity_available' => fake()->numberBetween(5, 200),
            'is_available' => true,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn () => ['is_available' => false]);
    }
}
