<x-layouts.site title="Leave a review">
    <div class="max-w-xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('account.orders.show', $order->order_number) }}" class="text-muted hover:text-ink">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
            <h1 class="text-xl font-bold text-ink">Leave a review</h1>
        </div>

        <div class="bg-white rounded-2xl border border-line p-6">
            <p class="text-sm text-muted mb-1">Order {{ $order->order_number }}</p>
            <p class="font-semibold text-ink mb-6">{{ $order->farmerProfile->farm_name }}</p>

            <form method="POST" action="{{ route('account.orders.review.store', $order->order_number) }}">
                @csrf

                {{-- Star rating --}}
                <div class="mb-6" x-data="{ rating: {{ old('rating', 0) }} }">
                    <label class="block text-sm font-semibold text-ink mb-3">Rating <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}"
                                class="transition-transform hover:scale-110 focus:outline-none"
                                :aria-label="'{{ $i }} star' + ({{ $i }} === 1 ? '' : 's')">
                                <svg class="h-9 w-9 transition-colors"
                                     :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-stone-200'"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" :value="rating">
                    @error('rating')
                        <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Comment --}}
                <div class="mb-6">
                    <label for="body" class="block text-sm font-semibold text-ink mb-1.5">Comment <span class="font-normal text-muted">(optional)</span></label>
                    <textarea id="body" name="body" rows="4"
                        class="block w-full rounded-xl border border-line px-3.5 py-2.5 text-[0.95rem] text-ink placeholder:text-muted/70 focus:border-brand-500 focus:ring-brand-500 @error('body') border-red-600 @enderror"
                        placeholder="How was the produce? Did the farmer communicate well?">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <x-ui.button class="w-full" size="lg">Submit review</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.site>
