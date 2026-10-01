<?php

namespace Tests\Feature\Auth;

use App\Enums\Province;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Thandeka Mokoena',
            'email' => 'Thandeka@Example.com',
            'phone' => '072 123 4567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'farm_name' => 'Mokoena Family Farm',
            'province' => Province::Limpopo->value,
            'municipality' => 'Polokwane',
            'town' => 'Seshego',
        ], $overrides);
    }

    public function test_farmer_registration_screen_renders(): void
    {
        $this->get(route('farmer.register'))
            ->assertOk()
            ->assertSee('Join as a farmer')
            ->assertSee('Limpopo');
    }

    public function test_farmer_can_register_with_a_farm_profile(): void
    {
        $response = $this->post(route('farmer.register.store'), $this->validData());

        $response->assertRedirect(route('farmer.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'thandeka@example.com')->firstOrFail();
        $this->assertSame(UserRole::Farmer, $user->role);
        $this->assertSame('0721234567', $user->phone);

        $farm = $user->farmerProfile;
        $this->assertNotNull($farm);
        $this->assertSame('mokoena-family-farm', $farm->slug);
        $this->assertSame(Province::Limpopo, $farm->province);
    }

    public function test_duplicate_farm_names_get_unique_slugs(): void
    {
        $this->post(route('farmer.register.store'), $this->validData());
        auth()->logout();
        $this->post(route('farmer.register.store'), $this->validData(['email' => 'second@example.com']));

        $slugs = \App\Models\FarmerProfile::pluck('slug')->all();
        $this->assertEqualsCanonicalizing(['mokoena-family-farm', 'mokoena-family-farm-2'], $slugs);
    }

    public function test_invalid_phone_and_province_are_rejected(): void
    {
        $this->post(route('farmer.register.store'), $this->validData([
            'phone' => '12345',
            'province' => 'atlantis',
        ]))->assertSessionHasErrors(['phone', 'province']);

        $this->assertGuest();
        $this->assertDatabaseCount('farmer_profiles', 0);
    }

    public function test_role_cannot_be_injected_through_registration(): void
    {
        $this->post(route('register'), [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->assertSame(UserRole::Customer, User::where('email', 'sneaky@example.com')->first()->role);
    }
}
