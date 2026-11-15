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
            $browser->visit('/register')
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

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password')
                    ->press('Log in')
                    ->assertAuthenticated()
                    ->visit('/logout')
                    ->assertGuest();
        });
    }

    public function test_invalid_credentials_show_error(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'nobody@example.com')
                    ->type('password', 'wrongpassword')
                    ->press('Log in')
                    ->assertSee('credentials do not match');
        });
    }

    public function test_farmer_dashboard_requires_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/farmer/dashboard')
                    ->assertPathBeginsWith('/login');
        });
    }
}
