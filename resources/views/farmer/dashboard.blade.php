@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = Str::before(auth()->user()->name, ' ');
    $addProductUrl = Route::has('farmer.products.create') ? route('farmer.products.create') : null;
    $ordersUrl = Route::has('farmer.orders.index') ? route('farmer.orders.index') : null;
@endphp

<x-layouts.dashboard title="Dashboard" area="farmer">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-brand-600">{{ $farmer->farm_name }}</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $greeting }}, {{ $firstName }}</h1>
        </div>
        @if ($addProductUrl)
            <x-ui.button :href="$addProductUrl" size="lg" class="w-full sm:w-auto">Add a product</x-ui.button>
        @endif
    </div>

    <section aria-labelledby="stats-heading" class="mt-8">
        <h2 id="stats-heading" class="sr-only">Your numbers</h2>
        <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-5">
            <x-dashboard.stat-card label="Pending orders" :value="$stats['pending_orders']" :href="$ordersUrl" highlight class="col-span-2 lg:col-span-1" />
            <x-dashboard.stat-card label="Total products" :value="$stats['total_products']" />
            <x-dashboard.stat-card label="Active products" :value="$stats['active_products']" />
            <x-dashboard.stat-card label="Completed orders" :value="$stats['completed_orders']" />
            <x-dashboard.stat-card label="Total orders" :value="$stats['total_orders']" />
        </div>
    </section>

    @if ($stats['total_products'] === 0)
        <x-ui.empty-state title="You haven't added any products yet" class="mt-8"
                          :action="$addProductUrl ? 'Add your first product' : null" :action-href="$addProductUrl">
            Add what you're harvesting this week. It takes about a minute.
        </x-ui.empty-state>
    @endif

    <section aria-labelledby="flow-heading" class="mt-10 rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
        <h2 id="flow-heading" class="text-lg font-bold">How selling works</h2>
        <ol class="mt-5 grid gap-4 sm:grid-cols-5 sm:gap-2">
            @foreach ([
                ['Add product', 'Photo, price and how much you have.'],
                ['Receive order', 'We notify you by email and here.'],
                ['Prepare', 'Confirm the order and pack it.'],
                ['Deliver', 'Take it to the customer\'s address.'],
                ['Collect cash', 'The customer pays you on delivery.'],
            ] as $i => [$step, $detail])
                <li class="relative flex gap-3 sm:flex-col sm:gap-2">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-800">{{ $i + 1 }}</span>
                    @unless ($loop->last)
                        <span class="absolute left-[calc(2rem+0.5rem)] right-2 top-4 hidden h-px bg-line sm:block" aria-hidden="true"></span>
                    @endunless
                    <div>
                        <p class="font-semibold text-ink">{{ $step }}</p>
                        <p class="mt-0.5 text-sm text-muted">{{ $detail }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>
</x-layouts.dashboard>
