@php
    $description = $farmerProfile->description
        ? Str::limit($farmerProfile->description, 160)
        : "Fresh produce from {$farmerProfile->farm_name} in {$farmerProfile->locationLabel()}.";
@endphp

<x-layouts.site :title="$farmerProfile->farm_name" :description="$description">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-muted" aria-label="Breadcrumb">
            <a href="{{ route('farmers.index') }}" class="hover:text-brand-600">Farmers</a>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
            <span class="text-ink">{{ $farmerProfile->farm_name }}</span>
        </nav>

        {{-- Farm header --}}
        <div class="mt-6 flex flex-col gap-5 rounded-card bg-brand-900 p-6 text-white sm:flex-row sm:items-center sm:p-8">
            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/10">
                <svg class="h-10 w-10 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>
            </div>
            <div class="min-w-0">
                <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $farmerProfile->farm_name }}</h1>
                <p class="mt-1 text-brand-200">{{ $farmerProfile->locationLabel() }}</p>
                @if ($farmerProfile->description)
                    <p class="mt-3 max-w-2xl text-sm text-brand-100">{{ $farmerProfile->description }}</p>
                @endif
            </div>
            <div class="shrink-0 text-right">
                <p class="text-3xl font-extrabold">{{ $products->count() }}</p>
                <p class="text-sm text-brand-200">{{ Str::plural('product', $products->count()) }}</p>
            </div>
        </div>

        {{-- Products --}}
        <div class="mt-8">
            <h2 class="text-xl font-bold text-ink">Available produce</h2>

            @if ($products->isEmpty())
                <x-ui.empty-state title="No products listed" class="mt-6">
                    This farm hasn't listed any products yet.
                </x-ui.empty-state>
            @else
                <ul class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" role="list">
                    @foreach ($products as $product)
                        <li>
                            <a href="{{ route('shop.show', $product) }}"
                               class="group flex flex-col overflow-hidden rounded-card bg-white shadow-card ring-1 ring-line/60 transition-shadow hover:shadow-md">
                                <div class="aspect-[4/3] overflow-hidden bg-brand-50">
                                    @if ($product->primaryImage)
                                        <img src="{{ $product->primaryImage->url }}"
                                             alt="{{ $product->primaryImage->alt_text ?: $product->name }}"
                                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center">
                                            <svg class="h-10 w-10 text-brand-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 7.5 9l4.5 6 3-4.5 4.5 6H2.25Z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-4">
                                    <p class="text-xs font-medium text-muted">{{ $product->category?->name }}</p>
                                    <p class="mt-1 font-semibold text-ink group-hover:text-brand-700">{{ $product->name }}</p>
                                    @if ($product->short_description)
                                        <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $product->short_description }}</p>
                                    @endif
                                    <p class="mt-auto pt-3 font-bold text-brand-700">{{ $product->priceLabel() }}</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Delivery info --}}
        <div class="mt-10 rounded-card bg-brand-50 p-5 ring-1 ring-brand-100">
            <h2 class="font-bold text-ink">Delivery information</h2>
            <p class="mt-2 text-sm text-muted">
                Delivery fee from {{ $farmerProfile->farm_name }}:
                <span class="font-semibold text-ink">R{{ number_format((float) $farmerProfile->delivery_fee, 2) }}</span>
            </p>
            <p class="mt-1 text-sm text-muted">Payment is <span class="font-semibold text-ink">cash on delivery</span>. You pay when your order arrives.</p>
        </div>
    </div>
</x-layouts.site>
