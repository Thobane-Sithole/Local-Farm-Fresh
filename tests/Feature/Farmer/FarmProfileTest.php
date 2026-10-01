<?php

namespace Tests\Feature\Farmer;

use App\Enums\Province;
use App\Enums\UserRole;
use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmProfileTest extends TestCase
{
    use RefreshDatabase;

    private function farmerWithProfile(): User
    {
        $user = User::factory()->create();
        $user->role = UserRole::Farmer;
        $user->save();
        FarmerProfile::factory()->for($user)->create();

        return $user;
    }

    public function test_farmer_can_view_profile_edit_page(): void
    {
        $farmer = $this->farmerWithProfile();

        $this->actingAs($farmer)
            ->get(route('farmer.profile.edit'))
            ->assertOk()
            ->assertSee('Farm profile');
    }

    public function test_customer_cannot_access_farm_profile(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('farmer.profile.edit'))
            ->assertForbidden();
    }

    public function test_farmer_can_update_profile(): void
    {
        $farmer = $this->farmerWithProfile();

        $this->actingAs($farmer)
            ->put(route('farmer.profile.update'), [
                'farm_name' => 'Green Valley Farm',
                'province' => Province::WesternCape->value,
                'municipality' => 'Stellenbosch',
                'town' => 'Stellenbosch',
                'farm_address' => '1 Vine Street',
                'delivery_fee' => '25.00',
            ])
            ->assertRedirect(route('farmer.profile.edit'));

        $this->assertEquals('Green Valley Farm', $farmer->farmerProfile->fresh()->farm_name);
    }

    public function test_farm_name_is_required(): void
    {
        $farmer = $this->farmerWithProfile();

        $this->actingAs($farmer)
            ->put(route('farmer.profile.update'), [
                'farm_name' => '',
                'province' => Province::Gauteng->value,
                'municipality' => 'Johannesburg',
                'delivery_fee' => '0',
            ])
            ->assertSessionHasErrors('farm_name');
    }
}
