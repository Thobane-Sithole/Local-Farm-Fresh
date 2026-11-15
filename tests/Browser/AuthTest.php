<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/register')
                    ->assertSee('Create your account')
                    ->type('name', 'Sipho Dlamini')
                    ->type('email', 'sipho@example.com')
                    ->type('phone', '0821234567')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('Create account')
                    ->assertAuthenticated();
        });
    }

    public function test_user_can_login_via_dusk_helper(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->logout()
                    ->loginAs($user)
                    ->assertAuthenticated();
        });
    }

    public function test_user_can_login_via_form(): void
    {
        User::factory()->create(['email' => 'formlogin@example.com']);

        $this->browse(function (Browser $browser) {
            // ->value() sets via JS (works for type="email"); form.submit()
            // bypasses Chrome's block on button-click when value was set by script.
            $browser->logout()->visit('/login')->waitFor('#email');
            $browser->value('#email', 'formlogin@example.com');
            $browser->value('#password', 'password');
            $browser->script("document.querySelector('form').submit()");
            $browser->pause(2000)->assertAuthenticated();
        });
    }

    public function test_invalid_credentials_show_error(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()->visit('/login')->waitFor('#email');
            $browser->value('#email', 'nobody@example.com');
            $browser->value('#password', 'wrongpassword');
            $browser->script("document.querySelector('form').submit()");
            $browser->pause(1000)->assertSee('These credentials do not match our records');
        });
    }

    public function test_farmer_area_requires_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/farmer')
                    ->assertPathBeginsWith('/login');
        });
    }

    public function test_admin_area_requires_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/admin')
                    ->assertPathBeginsWith('/login');
        });
    }
}
