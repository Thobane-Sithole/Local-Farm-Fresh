<x-layouts.dashboard :title="'Order '.$order->order_number">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('farmer.orders.index') }}" class="text-stone-400 hover:text-stone-600">
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

        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Status update panel --}}
        @if ($order->status->isOpen() && count($transitions) > 0)
            <div class="bg-white rounded-2xl border border-stone-200 p-5 mb-5" x-data="{ note: '' }">
                <h2 class="font-semibold text-stone-800 mb-3">Update status</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($transitions as $next)
                        <form method="POST" action="{{ route('farmer.orders.status', $order->order_number) }}"
                              x-on:submit="$event.target.querySelector('[name=note]').value = note">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $next->value }}">
                            <input type="hidden" name="note">
                            @if ($next === \App\Enums\OrderStatus::Cancelled)
                                <button type="button"
                                    x-on:click="note = prompt('Reason for cancellation (optional):') ?? ''; $el.closest('form').submit()"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition">
                                    Cancel order
                                </button>
                            @else
                                <button type="button"
                                    x-on:click="note = ''; $el.closest('form').submit()"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-brand-300 bg-brand-50 px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-100 transition">
                                    Mark as {{ $next->label() }}
                                </button>
                            @endif
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Customer & delivery --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <div class="bg-white rounded-2xl border border-stone-200 p-5">
                <h2 class="font-semibold text-stone-800 mb-2">Customer</h2>
                <p class="text-sm font-medium text-stone-700">{{ $order->customer->name }}</p>
                <p class="text-sm text-stone-500">{{ $order->customer->email }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-stone-200 p-5">
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
