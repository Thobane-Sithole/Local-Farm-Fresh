<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => fake()->words(4, true) . ' Market',
            'description' => fake()->paragraph(),
            'location'    => fake()->city() . ', South Africa',
            'starts_at'   => fake()->dateTimeBetween('+1 day', '+3 months'),
        ];
    }

    public function past(): static
    {
        return $this->state(fn () => ['starts_at' => fake()->dateTimeBetween('-3 months', '-1 day')]);
    }
}
