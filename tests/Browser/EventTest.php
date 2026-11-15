<?php

namespace Tests\Browser;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_event_detail_page_loads(): void
    {
        $event = Event::factory()->create([
            'title'    => 'Rosebank Farmers Market',
            'location' => 'Rosebank, Johannesburg',
        ]);

        $this->browse(function (Browser $browser) use ($event) {
            $browser->visit('/events/' . $event->id)
                    ->assertSee('Rosebank Farmers Market')
                    ->assertSee('Rosebank, Johannesburg');
        });
    }

    public function test_guest_sees_login_prompt_on_event_page(): void
    {
        $event = Event::factory()->create();

        $this->browse(function (Browser $browser) use ($event) {
            $browser->visit('/events/' . $event->id)
                    ->assertSee('Log in')
                    ->assertNotPresent('#event-comment');
        });
    }

    public function test_logged_in_user_can_comment_on_event(): void
    {
        $event = Event::factory()->create(['title' => 'Pretoria Market Day']);
        $user  = User::factory()->create(['username' => 'marketgoer']);

        $this->browse(function (Browser $browser) use ($event, $user) {
            $browser->loginAs($user)
                    ->visit('/events/' . $event->id)
                    ->assertPresent('#event-comment')
                    ->type('textarea', 'Excited to attend!')
                    ->press('Comment')
                    ->waitForText('Excited to attend!', 5)
                    ->assertSee('marketgoer');
        });
    }

    public function test_home_page_shows_upcoming_events_banner(): void
    {
        Event::factory()->create([
            'title'      => 'Cape Town Harvest Fair',
            'starts_at'  => now()->addDays(7),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Cape Town Harvest Fair');
        });
    }
}
