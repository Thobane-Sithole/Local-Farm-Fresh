<x-layouts.site :title="'Order '.$order->order_number">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('account.orders.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-xl font-bold text-brand-900">{{ $order->order_number }}</h1>
        </div>

        {{-- Tracking timeline --}}
        @if ($order->status !== \App\Enums\OrderStatus::Cancelled)
            @php $steps = \App\Enums\OrderStatus::trackingSteps(); @endphp
            <div class="bg-white rounded-2xl border border-stone-200 p-5 mb-5">
                <h2 class="text-sm font-semibold text-stone-700 mb-4">Tracking</h2>
                <div class="flex items-center gap-0">
                    @foreach ($steps as $i => $step)
                        @php
                            $stepOrder = array_search($step, $steps);
                            $currentOrder = array_search($order->status, $steps);
                            $done = $currentOrder !== false && $stepOrder <= $currentOrder;
                            $active = $step === $order->status;
                        @endphp
                        <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                            <div class="flex flex-col items-center">
                                <div class="h-7 w-7 rounded-full flex items-center justify-center
                                    {{ $active ? 'bg-brand-600 ring-2 ring-brand-300' : ($done ? 'bg-brand-600' : 'bg-stone-200') }}">
                                    @if ($done && ! $active)
                                        <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    @elseif ($active)
                                        <div class="h-2 w-2 rounded-full bg-white"></div>
                                    @endif
                                </div>
                                <p class="mt-1 text-center" style="font-size:10px; width:60px; line-height:1.2; {{ $active ? 'color:#3d7a4c; font-weight:600;' : 'color:#78716c;' }}">
                                    {{ $step->label() }}
                                </p>
                            </div>
                            @if ($i < count($steps) - 1)
                                <div class="flex-1 h-0.5 mx-1 {{ $done ? 'bg-brand-600' : 'bg-stone-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 mb-5">
                This order was cancelled.
                @if ($order->cancellation_reason)
                    Reason: {{ $order->cancellation_reason }}
                @endif
            </div>
        @endif

        {{-- Order items --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 mb-5">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold text-stone-800">Items</h2>
                <span class="text-sm text-stone-500">{{ $order->farmerProfile->farm_name }}</span>
            </div>
            <div class="divide-y divide-stone-100">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-2.5 text-sm">
                        <span class="text-stone-700">{{ $item->quantity }}× {{ $item->product_name }} <span class="text-stone-400">({{ $item->unit }})</span></span>
                        <span class="font-medium text-stone-800">R{{ number_format((float)$item->line_total, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t border-stone-100 space-y-1 text-sm">
                <div class="flex justify-between text-stone-500">
                    <span>Subtotal</span><span>R{{ number_format((float)$order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-stone-500">
                    <span>Delivery</span><span>R{{ number_format((float)$order->delivery_fee, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-stone-800 pt-1">
                    <span>Total</span><span>R{{ number_format((float)$order->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Delivery address --}}
        <div class="bg-white rounded-2xl border border-stone-200 p-5 mb-5">
            <h2 class="font-semibold text-stone-800 mb-2">Delivery address</h2>
            <address class="not-italic text-sm text-stone-600 space-y-0.5">
                <p class="font-medium">{{ $order->delivery_name }}</p>
                <p>{{ $order->delivery_phone }}</p>
                <p>{{ $order->delivery_street }}</p>
                @if ($order->delivery_suburb)<p>{{ $order->delivery_suburb }}</p>@endif
                <p>{{ $order->delivery_town }}@if($order->delivery_postal_code), {{ $order->delivery_postal_code }}@endif</p>
                <p>{{ $order->delivery_province->label() }}</p>
                @if ($order->delivery_notes)
                    <p class="mt-2 text-stone-400 italic">{{ $order->delivery_notes }}</p>
                @endif
            </address>
        </div>

        {{-- Status history --}}
        @if ($order->statusEvents->isNotEmpty())
            <div class="bg-white rounded-2xl border border-stone-200 p-5">
                <h2 class="font-semibold text-stone-800 mb-3">Status history</h2>
                <ol class="space-y-2">
                    @foreach ($order->statusEvents->sortByDesc('created_at') as $event)
                        <li class="flex items-start gap-3 text-sm">
                            <div class="mt-1 h-2 w-2 rounded-full bg-brand-400 flex-shrink-0"></div>
                            <div>
                                <span class="font-medium text-stone-700">{{ ($event->status instanceof \App\Enums\OrderStatus ? $event->status : \App\Enums\OrderStatus::from($event->status))->label() }}</span>
                                <span class="text-stone-400 ml-2">{{ $event->created_at->diffForHumans() }}</span>
                                @if ($event->note)
                                    <p class="text-stone-500 mt-0.5">{{ $event->note }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</x-layouts.site>
