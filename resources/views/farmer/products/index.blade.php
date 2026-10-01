<x-layouts.dashboard title="Products" area="farmer">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-brand-600">{{ $farmer->farm_name }}</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Products</h1>
        </div>
        @can('create', \App\Models\Product::class)
            <x-ui.button :href="route('farmer.products.create')" class="w-full sm:w-auto">Add a product</x-ui.button>
        @endcan
    </div>

    @if ($products->isEmpty())
        <x-ui.empty-state title="No products yet" class="mt-8"
                          action="Add your first product" :action-href="route('farmer.products.create')">
            Add what you're harvesting this week. It takes about a minute.
        </x-ui.empty-state>
    @else
        <div class="mt-6 overflow-hidden rounded-card bg-white shadow-card ring-1 ring-line/60">
            <table class="min-w-full divide-y divide-line">
                <thead>
                    <tr class="bg-brand-50">
                        <th scope="col" class="py-3.5 pl-5 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-muted">Product</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted sm:table-cell">Category</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted md:table-cell">Price</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted md:table-cell">Stock</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-5"><span class="sr-only">Edit</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($products as $product)
                        <tr class="{{ $product->trashed() ? 'bg-red-50/50' : '' }}">
                            <td class="py-3.5 pl-5 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-brand-100">
                                        @if ($product->primaryImage)
                                            <img src="{{ $product->primaryImage->url }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center">
                                                <svg class="h-5 w-5 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909"/></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-ink">{{ $product->name }}</p>
                                        @if ($product->trashed())
                                            <p class="text-xs text-red-600">Deleted</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted sm:table-cell">{{ $product->category?->name }}</td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted md:table-cell">{{ $product->priceLabel() }}</td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted md:table-cell">{{ $product->quantity_available }}</td>
                            <td class="px-3 py-3.5">
                                @if ($product->trashed())
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Deleted</span>
                                @elseif ($product->removed_at)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Removed</span>
                                @elseif ($product->is_available)
                                    <span class="inline-flex items-center rounded-full bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-800">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-line px-2 py-0.5 text-xs font-semibold text-muted">Off</span>
                                @endif
                            </td>
                            <td class="py-3.5 pl-3 pr-5 text-right text-sm">
                                @unless ($product->trashed())
                                    <a href="{{ route('farmer.products.edit', $product) }}" class="font-semibold text-brand-600 hover:text-brand-800">Edit</a>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="mt-6">{{ $products->links() }}</div>
        @endif
    @endif
</x-layouts.dashboard>
