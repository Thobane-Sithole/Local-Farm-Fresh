<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <x-layouts.head :title="$title ?? null" />
</head>
<body class="min-h-screen bg-white">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2">Skip to form</a>

    <div class="lg:grid lg:min-h-screen lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
        {{-- Brand panel: a slim band on phones, a full column on large screens --}}
        <aside class="bg-furrows px-5 py-5 text-white sm:px-8 lg:flex lg:flex-col lg:justify-between lg:px-12 lg:py-12">
            <a href="{{ route('home') }}" class="inline-flex rounded-lg">
                <x-brand.logo tone="light" />
            </a>

            <div class="hidden lg:block">
                @isset($aside)
                    {{ $aside }}
                @else
                    <p class="max-w-md text-4xl font-extrabold leading-[1.1] tracking-tight">
                        Fresh food from local farmers.
                    </p>
                    <p class="mt-4 max-w-sm text-lg text-brand-100">
                        Order from the people who grow it. Pay cash when it arrives at your door.
                    </p>
                @endisset
            </div>

            <p class="hidden text-sm text-brand-100/80 lg:block">Fresh. Local. Direct.</p>
        </aside>

        <main id="main" class="flex justify-center px-5 py-8 sm:px-8 sm:py-12 lg:items-center">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
