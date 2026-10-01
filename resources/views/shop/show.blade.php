@php
    $title       = $product->name.' – '.$product->farmerProfile?->farm_name;
    $description = $product->short_description ?: Str::limit(strip_tags($product->description), 160);
    $ogImage     = $product->primaryImage?->url;
@endphp

<x-layouts.site :title="$title" :description="$description" :og_image="$ogImage">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ route('shop.index') }}" class="hover:text-brand-600">Shop</a>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
            @if ($product->category)
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-brand-600">{{ $product->category->name }}</a>
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
            @endif
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="mt-6 grid gap-10 lg:grid-cols-2">

            {{-- Images --}}
            <div>
                @if ($product->images->isNotEmpty())
                    <div class="overflow-hidden rounded-card bg-brand-50 aspect-square">
                        <img src="{{ $product->images->first()->url }}"
                             alt="{{ $product->images->first()->alt_text ?: $product->name }}"
                             class="h-full w-full object-cover">
                    </div>
                    @if ($product->images->count() > 1)
                        <div class="mt-3 grid grid-cols-4 gap-2">
                            @foreach ($product->images->skip(1)->take(4) as $image)
                                <div class="overflow-hidden rounded-lg aspect-square bg-brand-50">
                                    <img src="{{ $image->url }}" alt="{{ $image->alt_text ?: $product->name }}"
                                         loading="lazy"
                                         class="h-full w-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="flex aspect-square items-center justify-center rounded-card bg-brand-50">
                        <svg class="h-20 w-20 text-brand-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 7.5 9l4.5 6 3-4.5 4.5 6H2.25Z"/></svg>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div>
                @if ($product->category)
                    <span class="text-sm font-semibold text-muted">{{ $product->category->name }}</span>
                @endif

                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-ink">{{ $product->name }}</h1>

                <p class="mt-3 text-2xl font-bold text-brand-700">{{ $product->priceLabel() }}</p>

                @if ($product->short_description)
                    <p class="mt-4 text-muted">{{ $product->short_description }}</p>
                @endif

                @if ($product->quantity_available > 0)
                    <p class="mt-3 text-sm text-muted">{{ $product->quantity_available }} {{ $product->unit->label() }} available</p>
                @endif

                {{-- Add to cart --}}
                <div class="mt-6" x-data="{ qty: 1 }">
                    <form method="POST" action="{{ route('cart.store') }}" class="flex items-center gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" x-model="qty">
                        <div class="flex items-center gap-1 rounded-xl border border-stone-300 px-2 py-1">
                            <button type="button" @click="qty = Math.max(1, qty - 1)"
                                class="h-8 w-8 rounded-lg text-stone-600 hover:bg-stone-100 flex items-center justify-center transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                            </button>
                            <span class="w-8 text-center text-sm font-semibold" x-text="qty">1</span>
                            <button type="button" @click="qty = qty + 1"
                                class="h-8 w-8 rounded-lg text-stone-600 hover:bg-stone-100 flex items-center justify-center transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </div>
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-base font-semibold text-white hover:bg-brand-700 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                            Add to cart
                        </button>
                    </form>
                </div>

                {{-- Farm info card --}}
                @if ($product->farmerProfile)
                    <div class="mt-6 rounded-card bg-brand-50 p-4 ring-1 ring-brand-100">
                        <a href="{{ route('farmers.show', $product->farmerProfile) }}"
                           class="group flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-ink group-hover:text-brand-700">{{ $product->farmerProfile->farm_name }}</p>
                                <p class="text-sm text-muted">{{ $product->farmerProfile->locationLabel() }}</p>
                            </div>
                        </a>
                        <p class="mt-3 text-sm text-muted">
                            Delivery fee: <span class="font-semibold text-ink">R{{ number_format((float) $product->farmerProfile->delivery_fee, 2) }}</span>
                            &nbsp;&middot;&nbsp; Cash on delivery
                        </p>
                    </div>
                @endif

                @if ($product->description)
                    <div class="mt-6 prose prose-sm max-w-none text-ink">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif
            </div>
        </div>

        {{-- Other products from this farm --}}
        @if ($otherProducts->isNotEmpty())
            <section class="mt-14">
                <h2 class="text-xl font-bold text-ink">
                    More from {{ $product->farmerProfile?->farm_name }}
                </h2>
                <ul class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" role="list">
                    @foreach ($otherProducts as $other)
                        <li>
                            <a href="{{ route('shop.show', $other) }}"
                               class="group flex flex-col overflow-hidden rounded-card bg-white shadow-card ring-1 ring-line/60 hover:shadow-md transition-shadow">
                                <div class="aspect-[4/3] overflow-hidden bg-brand-50">
                                    @if ($other->primaryImage)
                                        <img src="{{ $other->primaryImage->url }}" alt="{{ $other->name }}"
                                             class="h-full w-full object-cover transition-transform group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center">
                                            <svg class="h-8 w-8 text-brand-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 7.5 9l4.5 6 3-4.5 4.5 6H2.25Z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="font-semibold text-sm text-ink group-hover:text-brand-700">{{ $other->name }}</p>
                                    <p class="mt-0.5 text-sm font-bold text-brand-700">{{ $other->priceLabel() }}</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>
</x-layouts.site>
