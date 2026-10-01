<x-layouts.site title="My Orders">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-brand-900 mb-6">My Orders</h1>

        @if ($orders->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-stone-200">
                <p class="text-stone-500">You haven't placed any orders yet.</p>
                <a href="{{ route('shop.index') }}"
                   class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition">
                    Start shopping
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <a href="{{ route('account.orders.show', $order->order_number) }}"
                       class="block bg-white rounded-2xl border border-stone-200 p-5 hover:border-brand-300 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-stone-800">{{ $order->order_number }}</p>
                                <p class="text-sm text-stone-500 mt-0.5">{{ $order->farmerProfile->farm_name }}</p>
                                <p class="text-xs text-stone-400 mt-1">{{ $order->placed_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                @php
                                    $statusColors = [
                                        'pending'            => 'bg-amber-100 text-amber-700',
                                        'confirmed'          => 'bg-blue-100 text-blue-700',
                                        'preparing'          => 'bg-indigo-100 text-indigo-700',
                                        'ready_for_delivery' => 'bg-purple-100 text-purple-700',
                                        'out_for_delivery'   => 'bg-cyan-100 text-cyan-700',
                                        'delivered'          => 'bg-green-100 text-green-700',
                                        'cancelled'          => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$order->status->value] ?? 'bg-stone-100 text-stone-600' }}">
                                    {{ $order->status->label() }}
                                </span>
                                <p class="text-sm font-semibold text-stone-800 mt-2">R{{ number_format((float)$order->total, 2) }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-layouts.site>
