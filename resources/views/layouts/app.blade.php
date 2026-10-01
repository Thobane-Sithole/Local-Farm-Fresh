{{-- Breeze's authenticated layout (used by Profile settings), rendered in the brand shell. --}}
<x-layouts.site title="Account settings">
    @isset($header)
        <div class="border-b border-line bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 [&_h2]:text-2xl [&_h2]:font-extrabold [&_h2]:tracking-tight [&_h2]:text-ink">
                {{ $header }}
            </div>
        </div>
    @endisset

    {{ $slot }}
</x-layouts.site>
