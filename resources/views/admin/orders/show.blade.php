<x-layouts.dashboard :title="'Order '.$order->order_number" area="admin">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.orders.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-brand-900">{{ $order->order_number }}</h1>
                <p class="text-sm text-stone-500">Placed {{ $order->placed_at->format('d M Y, H:i') }}</p>
            </div>
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
            <span class="ml-auto inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $statusColors[$order->status->value] ?? '' }}">
                {{ $order->status->label() }}
            </span>
        </div>

        {{-- Customer, Farmer & delivery --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div class="bg-white rounded-2xl border border-stone-200 p-5">
                <h2 class="font-semibold text-stone-800 mb-2">Customer</h2>
                <p class="text-sm font-medium text-stone-700">{{ $order->customer->name }}</p>
                <p class="text-sm text-stone-500">{{ $order->customer->email }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-stone-200 p-5">
                <h2 class="font-semibold text-stone-800 mb-2">Farmer</h2>
                <p class="text-sm font-medium text-stone-700">{{ $order->farmerProfile->farm_name }}</p>
                <p class="text-sm text-stone-500">{{ $order->farmerProfile->user->name }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-stone-200 p-5 sm:col-span-2">
                <h2 class="font-semibold text-stone-800 mb-2">Delivery address</h2>
                <address class="not-italic text-sm text-stone-600 space-y-0.5">
                    <p>{{ $order->delivery_name }} · {{ $order->delivery_phone }}</p>
                    <p>{{ $order->delivery_street }}</p>
                    @if ($order->delivery_suburb)<p>{{ $order->delivery_suburb }}</p>@endif
                    <p>{{ $order->delivery_town }}{{ $order->delivery_postal_code ? ', '.$order->delivery_postal_code : '' }}</p>
                    <p>{{ $order->delivery_province->label() }}</p>
                    @if ($order->delivery_notes)
                        <p class="mt-1 italic text-stone-400">{{ $order->delivery_notes }}</p>
                    @endif
                </address>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 mb-5">
            <h2 class="font-semibold text-stone-800 mb-3">Items</h2>
            <div class="divide-y divide-stone-100">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-stone-700">{{ $item->quantity }}× {{ $item->product_name }}
                            <span class="text-stone-400">/ {{ $item->unit }}</span>
                        </span>
                        <div class="text-right">
                            <span class="text-stone-500">R{{ number_format((float)$item->unit_price, 2) }} each</span>
                            <span class="ml-3 font-medium text-stone-800">R{{ number_format((float)$item->line_total, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t border-stone-100 space-y-1 text-sm">
                <div class="flex justify-between text-stone-500"><span>Subtotal</span><span>R{{ number_format((float)$order->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-stone-500"><span>Delivery fee</span><span>R{{ number_format((float)$order->delivery_fee, 2) }}</span></div>
                <div class="flex justify-between font-semibold text-stone-800 pt-1"><span>Total</span><span>R{{ number_format((float)$order->total, 2) }}</span></div>
            </div>
        </div>

        {{-- Status history --}}
        @if ($order->statusEvents->isNotEmpty())
            <div class="bg-white rounded-2xl border border-stone-200 p-5">
                <h2 class="font-semibold text-stone-800 mb-3">Status history</h2>
                <ol class="space-y-2">
                    @foreach ($order->statusEvents->sortByDesc('created_at') as $event)
                        <li class="flex items-start gap-3 text-sm">
                            <div class="mt-1.5 h-2 w-2 rounded-full bg-brand-400 flex-shrink-0"></div>
                            <div>
                                <span class="font-medium text-stone-700">{{ ($event->status instanceof \App\Enums\OrderStatus ? $event->status : \App\Enums\OrderStatus::from($event->status))->label() }}</span>
                                <span class="text-stone-400 ml-2 text-xs">{{ $event->created_at->diffForHumans() }}</span>
                                @if ($event->note)<p class="text-stone-500 text-xs mt-0.5">{{ $event->note }}</p>@endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</x-layouts.dashboard>
