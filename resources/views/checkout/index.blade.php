<x-layouts.site title="Checkout">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-brand-900 mb-6">Checkout</h1>

        @if ($errors->any())
            <div class="mb-6 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700" role="alert">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}"
              x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Delivery address --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl border border-stone-200 p-6">
                        <h2 class="font-semibold text-stone-800 mb-4">Delivery address</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="checkout_name" class="block text-xs font-medium text-stone-600 mb-1">Full name *</label>
                                <input id="checkout_name" type="text" name="recipient_name" required
                                       value="{{ old('recipient_name', $defaultAddress?->recipient_name) }}"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label for="checkout_phone" class="block text-xs font-medium text-stone-600 mb-1">Phone *</label>
                                <input id="checkout_phone" type="tel" name="phone" required
                                       value="{{ old('phone', $defaultAddress?->phone) }}"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="checkout_street" class="block text-xs font-medium text-stone-600 mb-1">Street address *</label>
                                <input id="checkout_street" type="text" name="street_address" required
                                       value="{{ old('street_address', $defaultAddress?->street_address) }}"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label for="checkout_suburb" class="block text-xs font-medium text-stone-600 mb-1">Suburb</label>
                                <input id="checkout_suburb" type="text" name="suburb"
                                       value="{{ old('suburb', $defaultAddress?->suburb) }}"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label for="checkout_town" class="block text-xs font-medium text-stone-600 mb-1">Town / City *</label>
                                <input id="checkout_town" type="text" name="town" required
                                       value="{{ old('town', $defaultAddress?->town) }}"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label for="checkout_municipality" class="block text-xs font-medium text-stone-600 mb-1">Municipality</label>
                                <input id="checkout_municipality" type="text" name="municipality"
                                       value="{{ old('municipality', $defaultAddress?->municipality) }}"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label for="checkout_province" class="block text-xs font-medium text-stone-600 mb-1">Province *</label>
                                <select id="checkout_province" name="province" required
                                        class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="">— select —</option>
                                    @foreach ($provinces as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('province', $defaultAddress?->province?->value) === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="checkout_postal_code" class="block text-xs font-medium text-stone-600 mb-1">Postal code</label>
                                <input id="checkout_postal_code" type="text" name="postal_code"
                                       value="{{ old('postal_code', $defaultAddress?->postal_code) }}"
                                       class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="checkout_notes" class="block text-xs font-medium text-stone-600 mb-1">Delivery notes</label>
                                <textarea id="checkout_notes" name="delivery_notes" rows="2"
                                          class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('delivery_notes', $defaultAddress?->delivery_notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Payment --}}
                    <div class="bg-white rounded-2xl border border-stone-200 p-6">
                        <h2 class="font-semibold text-stone-800 mb-3">Payment</h2>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 h-4 w-4 rounded-full border-2 border-brand-600 bg-brand-600 flex items-center justify-center flex-shrink-0" aria-hidden="true">
                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-stone-800">Cash on delivery</p>
                                <p class="text-xs text-stone-500 mt-0.5">Pay when your order arrives. Bring the exact amount.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-stone-200 p-5 sticky top-6">
                        <h2 class="font-semibold text-stone-800 mb-4">Your order</h2>

                        @foreach ($groups as $items)
                            @php
                                $farmer = $items->first()->product->farmerProfile;
                                $sub    = $items->sum(fn($i) => $i->quantity * (float)$i->product->price);
                                $fee    = (float)$farmer->delivery_fee;
                            @endphp
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-brand-700 uppercase tracking-wide mb-1">{{ $farmer->farm_name }}</p>
                                @foreach ($items as $item)
                                    <div class="flex justify-between text-sm text-stone-600 mb-0.5">
                                        <span class="truncate mr-2">{{ $item->quantity }}× {{ $item->product->name }}</span>
                                        <span>R{{ number_format($item->quantity * (float)$item->product->price, 2) }}</span>
                                    </div>
                                @endforeach
                                <div class="flex justify-between text-xs text-stone-400 mt-1">
                                    <span>Delivery</span>
                                    <span>R{{ number_format($fee, 2) }}</span>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t border-stone-200 pt-3 flex justify-between font-semibold text-stone-800">
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

                        <button type="submit" :disabled="submitting"
                            :class="submitting ? 'opacity-60 cursor-not-allowed' : 'hover:bg-brand-700'"
                            class="mt-5 w-full rounded-lg bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition">
                            <span x-show="!submitting">Place order</span>
                            <span x-show="submitting" x-cloak>Placing order…</span>
                        </button>
                        <a href="{{ route('cart.index') }}" class="mt-2 block text-center text-xs text-stone-400 hover:text-stone-600">
                            ← Back to cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts.site>
