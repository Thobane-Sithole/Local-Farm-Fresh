{{-- Interim homepage. The full marketplace homepage is built in Phase 3. --}}
<x-layouts.site>
    <section class="bg-furrows text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 sm:py-20 lg:grid-cols-2 lg:items-center lg:px-8">
            <div>
                <h1 class="text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl lg:text-6xl">
                    Fresh food from local farmers.
                </h1>
                <p class="mt-5 max-w-lg text-lg text-brand-100">
                    Discover fresh, locally grown produce and support small-scale farmers in your community.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-ui.button :href="Route::has('shop.index') ? route('shop.index') : route('register')" variant="inverse" size="lg">Shop fresh produce</x-ui.button>
                    <x-ui.button :href="route('farmer.register')" size="lg" class="ring-1 ring-inset ring-white/30">Sell your produce</x-ui.button>
                </div>
            </div>

            @if ($farms->isNotEmpty())
                <div class="rounded-card bg-white p-5 text-ink shadow-card sm:p-6">
                    <h2 class="text-base font-bold">Farms selling here</h2>
                    <ul class="mt-4 divide-y divide-line">
                        @foreach ($farms as $farm)
                            <li class="flex items-center justify-between gap-4 py-3">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold">{{ $farm->farm_name }}</p>
                                    <p class="truncate text-sm text-muted">{{ $farm->locationLabel() }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-brand-100 px-2.5 py-1 text-xs font-semibold text-brand-800">
                                    {{ trans_choice(':count product|:count products', $farm->products_count) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>
</x-layouts.site>
