<?php

namespace Database\Factories;

use App\Enums\Province;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    private const PLACES = [
        ['Soweto', 'City of Johannesburg', Province::Gauteng, '1804'],
        ['Randburg', 'City of Johannesburg', Province::Gauteng, '2194'],
        ['Mamelodi', 'City of Tshwane', Province::Gauteng, '0122'],
        ['Umlazi', 'eThekwini', Province::KwaZuluNatal, '4031'],
        ['Khayelitsha', 'City of Cape Town', Province::WesternCape, '7784'],
        ['Polokwane', 'Polokwane', Province::Limpopo, '0699'],
        ['Mbombela', 'City of Mbombela', Province::Mpumalanga, '1200'],
        ['Mahikeng', 'Mahikeng', Province::NorthWest, '2745'],
        ['Mangaung', 'Mangaung', Province::FreeState, '9301'],
        ['Gqeberha', 'Nelson Mandela Bay', Province::EasternCape, '6001'],
    ];

    public function definition(): array
    {
        [$town, $municipality, $province, $postal] = fake()->randomElement(self::PLACES);

        return [
            'user_id' => User::factory(),
            'label' => 'Home',
            'recipient_name' => fake('en_ZA')->name(),
            'phone' => '0'.fake()->randomElement(['6', '7', '8']).fake()->numerify('########'),
            'street_address' => fake()->buildingNumber().' '.fake('en_ZA')->streetName(),
            'suburb' => null,
            'town' => $town,
            'municipality' => $municipality,
            'province' => $province,
            'postal_code' => $postal,
            'is_default' => true,
        ];
    }
}
