<?php

namespace Tests\Browser;

use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CommunityTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_community_feed_is_visible_to_guests(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/community')
                    ->assertSee('Community Hub');
        });
    }

    public function test_guest_sees_login_prompt_instead_of_post_form(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/community')
                    ->assertSee('Log in')
                    ->assertDontSee('Share something');
        });
    }

    public function test_user_without_username_is_prompted_to_set_one(): void
    {
        $user = User::factory()->create(['username' => null]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/community')
                    ->assertSee('username');
        });
    }

    public function test_user_with_username_can_submit_post_without_page_reload(): void
    {
        $user = User::factory()->create(['username' => 'farmerfan']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/community')
                    ->assertPresent('textarea')
                    ->type('textarea', 'Fresh tomatoes available this weekend!')
                    ->press('Post')
                    // New post appears instantly in the feed via Alpine
                    ->waitForText('Fresh tomatoes available this weekend!', 5)
                    ->assertSee('farmerfan');
        });
    }

    public function test_existing_post_is_visible_on_show_page(): void
    {
        $user = User::factory()->create(['username' => 'grower']);
        $post = CommunityPost::factory()->create([
            'user_id' => $user->id,
            'body'    => 'Organic carrots ready for pickup.',
        ]);

        $this->browse(function (Browser $browser) use ($post) {
            $browser->visit('/community/' . $post->id)
                    ->assertSee('Organic carrots ready for pickup.');
        });
    }

    public function test_user_can_comment_on_post_without_page_reload(): void
    {
        $author  = User::factory()->create(['username' => 'grower']);
        $post    = CommunityPost::factory()->create(['user_id' => $author->id, 'body' => 'Come visit the stall.']);
        $visitor = User::factory()->create(['username' => 'buyer']);

        $this->browse(function (Browser $browser) use ($post, $visitor) {
            $browser->loginAs($visitor)
                    ->visit('/community/' . $post->id)
                    ->type('textarea', 'What time do you open?')
                    ->press('Comment')
                    ->waitForText('What time do you open?', 5)
                    ->assertSee('buyer');
        });
    }
}
