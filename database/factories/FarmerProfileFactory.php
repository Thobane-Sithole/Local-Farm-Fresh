<?php

namespace Database\Factories;

use App\Enums\Province;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\FarmerProfile>
 */
class FarmerProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->farmer(),
            'farm_name' => fake('en_ZA')->lastName().' Family Farm',
            'description' => 'A small family farm growing seasonal produce for the local community.',
            'province' => fake()->randomElement(Province::cases()),
            'municipality' => fake('en_ZA')->city().' Local Municipality',
            'town' => fake('en_ZA')->city(),
            'delivery_fee' => fake()->randomElement([0, 25, 35, 50]),
        ];
    }
}
