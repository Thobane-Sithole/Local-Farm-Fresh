<x-layouts.site title="Shop fresh produce" description="Browse fresh, locally grown produce from small-scale South African farmers.">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">

        {{-- Page header + search --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">
                    {{ $currentCategory ? $currentCategory->name : 'All produce' }}
                </h1>
                <p class="mt-1 text-muted">
                    {{ $products->total() }} {{ Str::plural('product', $products->total()) }} available
                </p>
            </div>

            <form method="GET" action="{{ route('shop.index') }}" class="flex gap-2">
                @if ($currentCategory)
                    <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                @endif
                <div class="relative">
                    <input type="search" name="q" value="{{ request('q') }}"
                           placeholder="Search produce…"
                           class="block w-full rounded-xl border border-line bg-white py-2 pl-9 pr-3 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 sm:w-60">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/></svg>
                </div>
                <x-ui.button type="submit" size="sm">Search</x-ui.button>
            </form>
        </div>

        <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start">

            {{-- Category sidebar --}}
            <nav class="shrink-0 lg:w-56" aria-label="Categories">
                <ul class="space-y-0.5 rounded-card bg-white p-2 shadow-card ring-1 ring-line/60">
                    <li>
                        <a href="{{ route('shop.index', request()->only('q')) }}"
                           @class([
                               'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                               'bg-brand-600 text-white' => ! $currentCategory,
                               'text-ink hover:bg-brand-50' => $currentCategory,
                           ])>
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-brand-100 text-brand-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                            </span>
                            <span class="flex-1">All produce</span>
                        </a>
                    </li>
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('shop.index', array_merge(request()->only('q'), ['category' => $cat->slug])) }}"
                               @class([
                                   'flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-semibold transition-colors',
                                   'bg-brand-600 text-white' => $currentCategory?->id === $cat->id,
                                   'text-ink hover:bg-brand-50' => $currentCategory?->id !== $cat->id,
                               ])>
                                <span class="h-7 w-7 shrink-0 overflow-hidden rounded-lg">
                                    <img src="{{ $cat->imageUrl(56, 56) }}" alt="" aria-hidden="true" loading="lazy"
                                         class="h-full w-full object-cover">
                                </span>
                                <span class="flex-1 truncate">{{ $cat->name }}</span>
                                <span @class([
                                    'shrink-0 text-xs',
                                    'text-white/70' => $currentCategory?->id === $cat->id,
                                    'text-muted' => $currentCategory?->id !== $cat->id,
                                ])>{{ $cat->products_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Product grid --}}
            <div class="min-w-0 flex-1">
                @if ($products->isEmpty())
                    <x-ui.empty-state title="No produce found">
                        Try a different category or search term.
                    </x-ui.empty-state>
                @else
                    <ul class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" role="list" data-grid>
                        @foreach ($products as $product)
                            <li>
                                <a href="{{ route('shop.show', $product) }}"
                                   class="group flex flex-col overflow-hidden rounded-card bg-white shadow-card ring-1 ring-line/60 transition-shadow hover:shadow-md">
                                    {{-- Product image --}}
                                    <div class="aspect-[4/3] overflow-hidden bg-brand-50">
                                        @if ($product->primaryImage)
                                            <img src="{{ $product->primaryImage->url }}"
                                                 alt="{{ $product->primaryImage->alt_text ?: $product->name }}"
                                                 loading="lazy"
                                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                        @elseif ($product->category)
                                            <img src="{{ $product->category->imageUrl(400, 300) }}"
                                                 alt="{{ $product->category->name }}"
                                                 loading="lazy"
                                                 class="h-full w-full object-cover opacity-70 transition-transform duration-300 group-hover:scale-105">
                                        @else
                                            <div class="flex h-full items-center justify-center">
                                                <svg class="h-12 w-12 text-brand-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 7.5 9l4.5 6 3-4.5 4.5 6H2.25Z"/></svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-1 flex-col p-4">
                                        <p class="text-xs font-medium text-muted">{{ $product->category?->name }}</p>
                                        <p class="mt-1 font-semibold text-ink group-hover:text-brand-700">{{ $product->name }}</p>
                                        @if ($product->short_description)
                                            <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $product->short_description }}</p>
                                        @endif

                                        <div class="mt-auto flex items-end justify-between pt-3">
                                            <span class="font-bold text-brand-700">{{ $product->priceLabel() }}</span>
                                            <span class="text-xs text-muted">{{ $product->farmerProfile?->farm_name }}</span>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.site>
