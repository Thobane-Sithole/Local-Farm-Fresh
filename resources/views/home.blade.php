<x-layouts.site title="Farm-fresh produce, delivered to your door" description="Discover fresh, locally grown produce from small-scale South African farmers.">

    {{-- ─── Hero ──────────────────────────────────────────────────────────── --}}
    <section class="relative isolate flex min-h-[520px] items-center overflow-hidden">
        <img
            src="https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=1600&h=700&fit=crop&q=80&auto=format"
            alt=""
            aria-hidden="true"
            class="absolute inset-0 h-full w-full object-cover"
            fetchpriority="high"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-950/65 to-brand-950/10" aria-hidden="true"></div>

        <div class="relative mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="max-w-lg motion-hero">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-300">South Africa's farm marketplace</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-[1.05] tracking-tight text-white sm:text-5xl lg:text-[3.5rem]">
                    Fresh from the farm,<br>straight to your door.
                </h1>
                <p class="mt-5 max-w-md text-lg/relaxed text-white/80">
                    Seasonal produce grown close to home. Support local farmers and eat better.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-ui.button :href="Route::has('shop.index') ? route('shop.index') : route('register')" variant="inverse" size="lg">
                        Browse the shop
                    </x-ui.button>
                    <x-ui.button :href="route('farmer.register')" variant="glass" size="lg">
                        Start selling
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Browse by category ─────────────────────────────────────────────── --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-ink">Browse by category</h2>
                <p class="mt-1 text-sm text-muted">Fresh from South African farms.</p>
            </div>
            @if (Route::has('shop.index'))
                <a href="{{ route('shop.index') }}" class="hidden text-sm font-semibold text-brand-600 hover:text-brand-800 sm:block">View all →</a>
            @endif
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5" data-grid>
            @foreach ($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                   class="group relative aspect-square overflow-hidden rounded-2xl shadow-sm ring-1 ring-black/5 transition-all duration-200 hover:shadow-md hover:ring-brand-400/50">
                    <img
                        src="{{ $category->imageUrl(400, 400) }}"
                        alt="{{ $category->name }}"
                        loading="lazy"
                        class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-3">
                        <p class="text-sm font-bold leading-tight text-white">{{ $category->name }}</p>
                        @if ($category->products_count > 0)
                            <p class="mt-0.5 text-xs text-white/65">{{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        @if (Route::has('shop.index'))
            <div class="mt-4 text-center sm:hidden">
                <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-800">View all produce →</a>
            </div>
        @endif
    </section>

    {{-- ─── How it works ───────────────────────────────────────────────────── --}}
    <section class="border-y border-line/60 bg-brand-50/50 py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-2xl font-extrabold tracking-tight text-ink">How it works</h2>
            <p class="mt-2 text-center text-sm text-muted">From field to doorstep in three steps.</p>

            <div class="mt-10 grid gap-10 sm:grid-cols-3">
                @foreach ([
                    ['Browse & order', 'Find fresh produce from local farmers. Add to cart and place your order in minutes.', 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.954-7.13A60.114 60.114 0 0 0 7.5 14.25z'],
                    ['Farmer delivers', 'The farmer packs your order and delivers it fresh, straight to your address.', 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12'],
                    ['Pay on delivery', 'No upfront payment. Pay cash when your fresh produce arrives at your door.', 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z'],
                ] as [$step, $detail, $icon])
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-bold text-ink">{{ $step }}</h3>
                        <p class="mt-2 text-sm text-muted">{{ $detail }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── Farms selling here ──────────────────────────────────────────────── --}}
    @if ($farms->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <h2 class="text-2xl font-extrabold tracking-tight text-ink">Farms selling here</h2>
            <p class="mt-1 text-sm text-muted">Real farmers, real produce.</p>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5" data-grid>
                @foreach ($farms as $farm)
                    <div class="rounded-xl bg-white p-4 shadow-card ring-1 ring-line/60">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-100">
                                @if ($farm->profile_image_url ?? null)
                                    <img src="{{ $farm->profile_image_url }}" alt="{{ $farm->farm_name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="select-none text-sm font-extrabold text-brand-700">{{ Str::upper(Str::substr($farm->farm_name, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-ink">{{ $farm->farm_name }}</p>
                                <p class="truncate text-xs text-muted">{{ $farm->locationLabel() }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-800">
                                {{ trans_choice(':count product|:count products', $farm->products_count) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</x-layouts.site>
