<?php

namespace Tests\Feature\Admin;

use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_farmers_list(): void
    {
        $admin   = User::factory()->admin()->create();
        $profile = FarmerProfile::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.farmers.index'))
            ->assertOk()
            ->assertSee($profile->farm_name);
    }

    public function test_non_admin_cannot_access_admin_farmers_page(): void
    {
        $profile = FarmerProfile::factory()->create();

        $this->actingAs($profile->user)
            ->get(route('admin.farmers.index'))
            ->assertForbidden();
    }

    public function test_admin_can_verify_a_farmer(): void
    {
        $admin   = User::factory()->admin()->create();
        $profile = FarmerProfile::factory()->create();
        $profile->is_verified = false;
        $profile->save();

        $this->actingAs($admin)
            ->post(route('admin.farmers.verify', $profile))
            ->assertRedirect();

        $this->assertTrue($profile->fresh()->is_verified);
    }

    public function test_admin_can_unverify_a_farmer(): void
    {
        $admin   = User::factory()->admin()->create();
        $profile = FarmerProfile::factory()->create();
        $profile->is_verified = true;
        $profile->save();

        $this->actingAs($admin)
            ->post(route('admin.farmers.unverify', $profile))
            ->assertRedirect();

        $this->assertFalse($profile->fresh()->is_verified);
    }

    public function test_customer_cannot_verify_farmers(): void
    {
        $customer = User::factory()->create();
        $profile  = FarmerProfile::factory()->create();

        $this->actingAs($customer)
            ->post(route('admin.farmers.verify', $profile))
            ->assertForbidden();
    }
}
