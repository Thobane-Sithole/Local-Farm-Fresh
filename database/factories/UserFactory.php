<?php

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake('en_ZA')->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '0'.fake()->randomElement(['6', '7', '8']).fake()->numerify('########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => UserRole::Customer,
            'status' => AccountStatus::Active,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function farmer(): static
    {
        return $this->state(fn () => ['role' => UserRole::Farmer]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => UserRole::Admin]);
    }

    public function suspended(): static
    {
        return $this->state(fn () => [
            'status' => AccountStatus::Suspended,
            'suspended_at' => now(),
        ]);
    }
}
