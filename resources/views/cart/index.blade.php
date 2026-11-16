<x-layouts.site title="Your Cart">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-brand-900 mb-6">Your Cart</h1>

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

        @if (session('cart_conflict'))
            @php $conflict = session('cart_conflict'); @endphp
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-amber-900">Your cart has items from a different farm</p>
                        <p class="mt-1 text-sm text-amber-800">
                            To add <strong>{{ $conflict['product_name'] }}</strong> from <strong>{{ $conflict['farmer_name'] }}</strong>,
                            you need to clear your current cart and start a new order.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('cart.replace') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $conflict['product_id'] }}">
                                <input type="hidden" name="quantity" value="{{ $conflict['quantity'] }}">
                                <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition">
                                    Clear cart &amp; order from {{ $conflict['farmer_name'] }}
                                </button>
                            </form>
                            <a href="{{ route('shop.index') }}"
                               class="inline-flex items-center rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-50 transition">
                                Keep current cart
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($cart->items->isEmpty())
            <div class="text-center py-24 bg-white rounded-2xl border border-stone-200">
                <svg class="mx-auto h-16 w-16 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                <p class="mt-4 text-stone-500 text-lg">Your cart is empty.</p>
                <a href="{{ route('shop.index') }}" class="mt-6 inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
                    Browse products
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Cart items --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($groups as $farmerId => $items)
                        @php $farmer = $items->first()->product->farmerProfile; @endphp
                        <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
                            <div class="px-5 py-3 bg-stone-50 border-b border-stone-200 flex items-center gap-2">
                                <span class="text-sm font-semibold text-brand-800">{{ $farmer->farm_name }}</span>
                                <span class="text-xs text-stone-400">· R{{ number_format((float)$farmer->delivery_fee, 2) }} delivery</span>
                            </div>
                            <div class="divide-y divide-stone-100">
                                @foreach ($items as $item)
                                    <div class="flex items-center gap-4 px-5 py-4">
                                        @if ($item->product->images->isNotEmpty())
                                            <img src="{{ $item->product->images->first()->url }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="h-16 w-16 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-stone-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="h-8 w-8 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21l6.75-6.75M21 21H3m18 0V9.75M3 21V9.75m0 0A2.25 2.25 0 015.25 7.5h13.5A2.25 2.25 0 0121 9.75" /></svg>
                                            </div>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-stone-800 truncate">{{ $item->product->name }}</p>
                                            <p class="text-sm text-stone-500">R{{ number_format((float)$item->product->price, 2) }} / {{ $item->product->unit->label() }}</p>
                                        </div>

                                        {{-- Quantity stepper --}}
                                        <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center gap-1">
                                            @csrf @method('PATCH')
                                            <button type="submit" name="quantity" value="{{ max(0, $item->quantity - 1) }}"
                                                class="h-7 w-7 rounded-md border border-stone-300 text-stone-600 hover:bg-stone-50 flex items-center justify-center">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                            </button>
                                            <span class="w-8 text-center text-sm font-medium">{{ $item->quantity }}</span>
                                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                                class="h-7 w-7 rounded-md border border-stone-300 text-stone-600 hover:bg-stone-50 flex items-center justify-center">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                            </button>
                                        </form>

                                        <p class="w-20 text-right text-sm font-semibold text-stone-800">
                                            R{{ number_format($item->quantity * (float)$item->product->price, 2) }}
                                        </p>

                                        <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-stone-400 hover:text-red-500 transition" title="Remove">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order summary sidebar --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-stone-200 p-5 sticky top-6">
                        <h2 class="font-semibold text-stone-800 mb-4">Order summary</h2>

                        <div class="space-y-2 text-sm">
                            @foreach ($groups as $items)
                                @php
                                    $farmer = $items->first()->product->farmerProfile;
                                    $sub    = $items->sum(fn($i) => $i->quantity * (float)$i->product->price);
                                    $fee    = (float)$farmer->delivery_fee;
                                @endphp
                                <div class="flex justify-between text-stone-600">
                                    <span class="truncate mr-2">{{ $farmer->farm_name }}</span>
                                    <span>R{{ number_format($sub, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-stone-400 pl-2">
                                    <span>Delivery</span>
                                    <span>R{{ number_format($fee, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-4 border-t border-stone-200 flex justify-between font-semibold text-stone-800">
                            <span>Total</span>
                            <span>
                                R{{ number_format(
                                    $groups->sum(fn($items) =>
                                        $items->sum(fn($i) => $i->quantity * (float)$i->product->price)
                                        + (float)$items->first()->product->farmerProfile->delivery_fee
                                    ), 2
                                ) }}
                            </span>
                        </div>

                        <div class="mt-2 text-xs text-stone-400">
                            Payment: Cash on delivery
                        </div>

                        @auth
                            <a href="{{ route('checkout.index') }}"
                               class="mt-5 block w-full text-center rounded-lg bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700 transition">
                                Proceed to checkout
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="mt-5 block w-full text-center rounded-lg bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700 transition">
                                Log in to checkout
                            </a>
                            <p class="mt-2 text-xs text-center text-stone-400">
                                Your cart is saved as a guest.
                            </p>
                        @endauth
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.site>
