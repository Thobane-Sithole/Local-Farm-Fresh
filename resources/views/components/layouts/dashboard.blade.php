@props(['title', 'area' => 'farmer'])

@php
    $icon = [
        'home' => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1v-9.5Z',
        'box' => 'M3 7.5 12 3l9 4.5v9L12 21l-9-4.5v-9Zm0 0 9 4.5m0 0 9-4.5M12 12v9',
        'plus' => 'M12 5v14M5 12h14',
        'receipt' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6m-6 4h6',
        'bell' => 'M6 16V11a6 6 0 1 1 12 0v5l2 2H4l2-2Zm4 4h4',
        'user' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0',
        'users' => 'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-6 10a6 6 0 0 1 12 0m2-10a3 3 0 1 0 0-6m1 16h3a5 5 0 0 0-4-5',
        'tag' => 'M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9-9-9Zm5-5h.01',
        'cog'   => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.4-3a7.4 7.4 0 0 0-.1-1.2l2-1.6-2-3.4-2.4 1a7.5 7.5 0 0 0-2-1.2L14.5 3h-5l-.4 2.6a7.5 7.5 0 0 0-2 1.2l-2.4-1-2 3.4 2 1.6a7.4 7.4 0 0 0 0 2.4l-2 1.6 2 3.4 2.4-1a7.5 7.5 0 0 0 2 1.2l.4 2.6h5l.4-2.6a7.5 7.5 0 0 0 2-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2Z',
        'chart' => 'M3 3v18h18M7 16l4-4 4 4 4-4',
    ];

    $nav = [
        'farmer' => [
            ['Dashboard', 'farmer.dashboard', 'home'],
            ['Products', 'farmer.products.index', 'box'],
            ['Add product', 'farmer.products.create', 'plus'],
            ['Orders', 'farmer.orders.index', 'receipt'],
            ['Analytics', 'farmer.analytics', 'chart'],
            ['Notifications', 'notifications.index', 'bell'],
            ['Farm profile', 'farmer.profile.edit', 'user'],
            ['Settings', 'profile.edit', 'cog'],
        ],
        'admin' => [
            ['Dashboard', 'admin.dashboard', 'home'],
            ['Users', 'admin.users.index', 'users'],
            ['Farmers', 'admin.farmers.index', 'user'],
            ['Products', 'admin.products.index', 'box'],
            ['Categories', 'admin.categories.index', 'tag'],
            ['Orders', 'admin.orders.index', 'receipt'],
            ['Settings', 'profile.edit', 'cog'],
        ],
    ][$area];

    // Only show links whose pages exist yet (new phases add routes).
    $nav = array_filter($nav, fn ($item) => Route::has($item[1]));

    $unreadNotifications = Route::has('notifications.index')
        ? auth()->user()?->unreadNotifications()->count() ?? 0
        : 0;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head :title="$title" :noindex="true" />
</head>
<body x-data="{ drawer: false }" class="min-h-screen">
    {{-- Loading bar --}}
    <div id="progress-bar" aria-hidden="true" style="position:fixed;top:0;left:0;height:3px;width:0;background:#2E9B50;z-index:9999;transition:width 300ms ease,opacity 400ms ease;pointer-events:none"></div>
    <script>
    (function(){var el=document.getElementById('progress-bar'),t1,t2;function start(){clearTimeout(t1);clearTimeout(t2);el.style.opacity='1';el.style.width='20%';t1=setTimeout(function(){el.style.width='60%';},250);t2=setTimeout(function(){el.style.width='82%';},700);}function done(){clearTimeout(t1);clearTimeout(t2);el.style.width='100%';setTimeout(function(){el.style.opacity='0';setTimeout(function(){el.style.width='0';},400);},200);}document.addEventListener('click',function(e){var a=e.target.closest('a[href]');if(a&&a.href&&a.target!=='_blank'&&!e.ctrlKey&&!e.metaKey&&!e.shiftKey&&!a.href.startsWith('#')&&!a.href.startsWith('javascript')){start();}});document.addEventListener('submit',start);window.addEventListener('pageshow',done);})();
    </script>

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2">Skip to content</a>

    {{-- Mobile top bar --}}
    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-line bg-white px-4 lg:hidden">
        <a href="{{ route('dashboard') }}" class="rounded-lg"><x-brand.logo /></a>
        <button type="button" @click="drawer = true" class="-mr-2 inline-flex h-11 w-11 items-center justify-center rounded-lg" aria-controls="sidebar" :aria-expanded="drawer.toString()">
            <span class="sr-only">Open menu</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </header>

    {{-- Drawer backdrop (mobile) --}}
    <div x-show="drawer" x-cloak x-transition.opacity @click="drawer = false" class="fixed inset-0 z-40 bg-brand-900/50 lg:hidden" aria-hidden="true"></div>

    {{-- Sidebar --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-brand-900 text-brand-100 transition-transform duration-200 lg:translate-x-0"
           :class="drawer && '!translate-x-0'"
           @keydown.escape.window="drawer = false">
        <div class="flex h-16 items-center justify-between px-5 lg:h-20">
            <a href="{{ route('dashboard') }}" class="rounded-lg"><x-brand.logo tone="light" /></a>
            <button type="button" @click="drawer = false" class="inline-flex h-11 w-11 items-center justify-center rounded-lg lg:hidden">
                <span class="sr-only">Close menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-2" aria-label="{{ ucfirst($area) }}">
            <ul class="space-y-1">
                @foreach ($nav as [$label, $route, $iconName])
                    @php $active = request()->routeIs($route); @endphp
                    <li>
                        <a href="{{ route($route) }}"
                           @class([
                               'flex min-h-[46px] items-center gap-3 rounded-xl px-3 text-[0.95rem] font-semibold transition-colors',
                               'bg-white/10 text-white' => $active,
                               'hover:bg-white/5 hover:text-white' => ! $active,
                           ])
                           @if ($active) aria-current="page" @endif>
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-brand-300' : 'text-brand-100/70' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $icon[$iconName] }}"/></svg>
                            <span class="flex-1">{{ $label }}</span>
                            @if ($iconName === 'bell' && $unreadNotifications > 0)
                                <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                                    {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="border-t border-white/10 p-4">
            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs text-brand-100/80">{{ auth()->user()->email }}</p>
            {{-- Dark mode toggle --}}
            <button x-data @click="$store.darkMode.toggle()"
                    class="mt-3 flex min-h-[44px] w-full items-center gap-3 rounded-xl px-3 text-sm font-semibold ring-1 ring-inset ring-white/20 hover:bg-white/5"
                    :aria-label="$store.darkMode.on ? 'Switch to light mode' : 'Switch to dark mode'">
                <svg x-show="!$store.darkMode.on" class="h-4 w-4 shrink-0 text-brand-100/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg x-show="$store.darkMode.on" x-cloak class="h-4 w-4 shrink-0 text-brand-100/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 0 0 10A5 5 0 0 0 12 7z"/></svg>
                <span x-text="$store.darkMode.on ? 'Light mode' : 'Dark mode'"></span>
            </button>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="flex min-h-[44px] w-full items-center justify-center rounded-xl text-sm font-semibold ring-1 ring-inset ring-white/20 hover:bg-white/5">Log out</button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-72">
        <main id="main" class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8 lg:px-10 lg:py-10">
            <x-ui.flash class="mb-6" />
            {{ $slot }}
        </main>
    </div>
    @stack('scripts')
</body>
</html>
