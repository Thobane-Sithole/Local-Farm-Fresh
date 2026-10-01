<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\Province;
use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * For tests. Real orders are created through OrderService (Phase 4).
 *
 * @extends Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => 'LFF-'.now()->format('Ymd').'-'.fake()->unique()->numerify('9###'),
            'checkout_group' => (string) Str::uuid(),
            'customer_id' => User::factory(),
            'farmer_profile_id' => FarmerProfile::factory(),
            'delivery_name' => fake('en_ZA')->name(),
            'delivery_phone' => '0721234567',
            'delivery_street' => '12 Vilakazi Street',
            'delivery_town' => 'Soweto',
            'delivery_province' => Province::Gauteng,
            'subtotal' => 100,
            'delivery_fee' => 25,
            'total' => 125,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'status' => OrderStatus::Pending,
            'placed_at' => now(),
        ];
    }
}
