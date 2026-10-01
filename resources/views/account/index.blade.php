<x-layouts.site title="My account">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <x-ui.flash class="mb-6" />

        <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Hi, {{ Str::before($user->name, ' ') }}</h1>
        <p class="mt-1 text-muted">Your orders and delivery details live here.</p>

        <section aria-labelledby="orders-heading" class="mt-8">
            <div class="flex items-center justify-between mb-3">
                <h2 id="orders-heading" class="text-lg font-bold">Your orders</h2>
                @if (Route::has('account.orders.index') && $recentOrders->isNotEmpty())
                    <a href="{{ route('account.orders.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-800">View all →</a>
                @endif
            </div>
            @if ($recentOrders->isEmpty())
                <x-ui.empty-state title="No orders yet" class="mt-4"
                                  action="Browse fresh produce" :action-href="Route::has('shop.index') ? route('shop.index') : route('home')">
                    When you order from a farmer, you'll be able to follow it here from confirmation to your door.
                </x-ui.empty-state>
            @else
                <div class="space-y-3">
                    @foreach ($recentOrders as $order)
                        <a href="{{ route('account.orders.show', $order->order_number) }}"
                           class="flex items-center justify-between rounded-xl border border-stone-200 bg-white px-4 py-3 hover:border-brand-300 transition">
                            <div>
                                <p class="text-sm font-semibold text-stone-800">{{ $order->order_number }}</p>
                                <p class="text-xs text-stone-500">{{ $order->farmerProfile->farm_name }} · {{ $order->placed_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                @php
                                    $sc = ['pending'=>'bg-amber-100 text-amber-700','confirmed'=>'bg-blue-100 text-blue-700','preparing'=>'bg-indigo-100 text-indigo-700','ready_for_delivery'=>'bg-purple-100 text-purple-700','out_for_delivery'=>'bg-cyan-100 text-cyan-700','delivered'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700'];
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $sc[$order->status->value] ?? '' }}">{{ $order->status->label() }}</span>
                                <p class="text-xs font-medium text-stone-700 mt-0.5">R{{ number_format((float)$order->total, 2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="mt-8 flex flex-wrap gap-3">
            <x-ui.button :href="route('profile.edit')" variant="secondary">Account settings</x-ui.button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ui.button variant="ghost">Log out</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.site>
