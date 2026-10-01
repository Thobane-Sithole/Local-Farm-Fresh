<?php

namespace Tests\Feature\Authorization;

use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function farmer(): User
    {
        return FarmerProfile::factory()->create()->user;
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('farmer.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('account.index'))->assertRedirect(route('login'));
    }

    public function test_dashboard_redirects_each_role_home(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')->assertRedirect(route('account.index'));
        $this->actingAs($this->farmer())->get('/dashboard')->assertRedirect(route('farmer.dashboard'));
        $this->actingAs(User::factory()->admin()->create())->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    public function test_customer_cannot_open_farmer_or_admin_areas(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)->get(route('account.index'))->assertOk();
        $this->actingAs($customer)->get(route('farmer.dashboard'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_farmer_sees_their_dashboard_but_not_admin(): void
    {
        $farmer = $this->farmer();

        $this->actingAs($farmer)->get(route('farmer.dashboard'))
            ->assertOk()
            ->assertSee($farmer->farmerProfile->farm_name);
        $this->actingAs($farmer)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_sees_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Platform overview');
    }

    public function test_suspended_user_is_signed_out_immediately(): void
    {
        $farmer = $this->farmer();
        $farmer->suspend('Test');

        $this->actingAs($farmer)->get(route('farmer.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_reinstated_user_regains_access(): void
    {
        $farmer = $this->farmer();
        $farmer->suspend();
        $farmer->reinstate();

        $this->actingAs($farmer)->get(route('farmer.dashboard'))->assertOk();
    }
}
