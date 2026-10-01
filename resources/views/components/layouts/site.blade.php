@props(['title' => null, 'description' => null, 'og_image' => null])

@php
    // Links appear as their pages are built in later phases.
    $links = collect([
        ['Home', 'home'],
        ['Shop', 'shop.index'],
        ['Categories', 'categories.index'],
        ['Farmers', 'farmers.index'],
        ['About', 'about'],
    ])->filter(fn ($l) => Route::has($l[1]));
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head :title="$title" :description="$description" :og_image="$og_image" />
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2">Skip to content</a>

    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-line bg-white/95 backdrop-blur">
        <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Main">
            <a href="{{ route('home') }}" class="rounded-lg"><x-brand.logo /></a>

            <ul class="hidden items-center gap-1 md:flex">
                @foreach ($links as [$label, $route])
                    <li>
                        <a href="{{ route($route) }}"
                           @class([
                               'rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                               'text-brand-600' => request()->routeIs($route),
                               'text-ink hover:text-brand-600' => ! request()->routeIs($route),
                           ])
                           @if (request()->routeIs($route)) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="hidden items-center gap-2 md:flex">
                @auth
                    @if (Route::has('notifications.index'))
                        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                        <a href="{{ route('notifications.index') }}" class="relative rounded-lg p-2 text-ink hover:text-brand-600 transition"
                           aria-label="{{ $unreadCount > 0 ? "Notifications ($unreadCount unread)" : 'Notifications' }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                            @if ($unreadCount > 0)
                                <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endauth
                @if (Route::has('cart.index'))
                    @php
                        $cartCount = 0;
                        if (auth()->check()) {
                            $cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0;
                        }
                    @endphp
                    <a href="{{ route('cart.index') }}" class="relative rounded-lg p-2 text-ink hover:text-brand-600 transition"
                       aria-label="{{ $cartCount > 0 ? "Cart ($cartCount items)" : 'Cart' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                        @if ($cartCount > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-brand-600 text-[10px] font-bold text-white">
                                {{ $cartCount > 9 ? '9+' : $cartCount }}
                            </span>
                        @endif
                    </a>
                @endif
                @auth
                    <x-ui.button :href="route('dashboard')" variant="secondary" size="sm">My account</x-ui.button>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-ink hover:text-brand-600">Log in</a>
                    <x-ui.button :href="route('register')" size="sm">Sign up</x-ui.button>
                @endauth
            </div>

            <button type="button" class="-mr-2 inline-flex h-11 w-11 items-center justify-center rounded-lg text-ink md:hidden"
                    @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-menu">
                <span class="sr-only">Menu</span>
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </nav>

        <div id="mobile-menu" x-show="open" x-cloak @keydown.escape.window="open = false" class="border-t border-line bg-white px-4 pb-5 pt-2 md:hidden">
            <ul class="space-y-1">
                @foreach ($links as [$label, $route])
                    <li><a href="{{ route($route) }}" class="block rounded-lg px-3 py-3 text-base font-semibold text-ink hover:bg-brand-50">{{ $label }}</a></li>
                @endforeach
            </ul>
            <div class="mt-3 grid gap-2">
                @auth
                    <x-ui.button :href="route('dashboard')" variant="secondary">My account</x-ui.button>
                @else
                    <x-ui.button :href="route('register')">Sign up</x-ui.button>
                    <x-ui.button :href="route('login')" variant="secondary">Log in</x-ui.button>
                @endauth
            </div>
        </div>
    </header>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-brand-900 text-brand-100">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-10 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <div>
                <x-brand.logo tone="light" />
                <p class="mt-3 max-w-xs text-sm">From local farmers to your doorstep.</p>
            </div>
            <ul class="flex flex-wrap gap-x-6 gap-y-2 text-sm font-medium">
                <li><a href="{{ route('farmer.register') }}" class="hover:text-white">Sell on Local-Farm-Fresh</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-white">Log in</a></li>
            </ul>
        </div>
        <div class="border-t border-white/10">
            <p class="mx-auto max-w-7xl px-4 py-4 text-xs text-brand-100/80 sm:px-6 lg:px-8">
                &copy; {{ now()->year }} Local-Farm-Fresh. Payment is cash on delivery.
            </p>
        </div>
    </footer>
</body>
</html>
