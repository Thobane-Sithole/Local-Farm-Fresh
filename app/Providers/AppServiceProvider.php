<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusAdvanced;
use App\Listeners\SendOrderPlacedNotifications;
use App\Listeners\SendOrderStatusNotifications;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Outside production, throw on lazy loading (catches N+1 queries early),
        // on silently discarded attributes, and on missing attributes.
        Model::shouldBeStrict(! $this->app->isProduction());

        // Render terminates TLS at its proxy; make sure generated URLs use https.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        Password::defaults(fn () => $this->app->isProduction()
            ? Password::min(8)->letters()->numbers()->uncompromised()
            : Password::min(8));

        // Admins can manage the entire platform. Returning null (not false)
        // lets the normal policies decide for everyone else.
        Gate::before(fn (User $user) => $user->isAdmin() ? true : null);

        Event::listen(OrderPlaced::class, SendOrderPlacedNotifications::class);
        Event::listen(OrderStatusAdvanced::class, SendOrderStatusNotifications::class);
    }
}
