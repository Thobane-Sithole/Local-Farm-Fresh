<x-layouts.site title="Order Confirmed">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 text-center">
        <div class="inline-flex h-16 w-16 rounded-full bg-green-100 items-center justify-center mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-brand-900">Order placed!</h1>
        <p class="mt-2 text-stone-500">
            Thank you for shopping local. Your
            {{ $orders->count() === 1 ? 'order has' : $orders->count().' orders have' }}
            been sent to the farmer{{ $orders->count() > 1 ? 's' : '' }}.
        </p>

        <div class="mt-8 space-y-4 text-left">
            @foreach ($orders as $order)
                <div class="bg-white rounded-2xl border border-stone-200 p-5">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div>
                            <p class="font-semibold text-stone-800">{{ $order->order_number }}</p>
                            <p class="text-sm text-stone-500">{{ $order->farmerProfile->farm_name }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                            Pending
                        </span>
                    </div>
                    <div class="divide-y divide-stone-100 text-sm">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between py-1.5 text-stone-600">
                                <span>{{ $item->quantity }}× {{ $item->product_name }}</span>
                                <span>R{{ number_format((float)$item->line_total, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-3 border-t border-stone-200 flex justify-between text-sm">
                        <span class="text-stone-500">Total (incl. delivery)</span>
                        <span class="font-semibold text-stone-800">R{{ number_format((float)$order->total, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('account.orders.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
                View my orders
            </a>
            <a href="{{ route('shop.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50 transition">
                Continue shopping
            </a>
        </div>
    </div>
</x-layouts.site>
