<x-layouts.dashboard title="Products" area="admin">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-brand-900">Products</h1>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            {{-- Status tabs --}}
            <div class="flex items-center gap-1">
                @foreach (['active' => 'Active', 'removed' => 'Removed'] as $val => $label)
                    <a href="{{ route('admin.products.index', array_merge(request()->only('search'), ['status' => $val])) }}"
                       class="whitespace-nowrap rounded-full px-3 py-1 text-sm font-medium transition
                           {{ $status === $val
                               ? 'bg-brand-600 text-white'
                               : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        {{ $label }} ({{ $counts[$val] }})
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex gap-2 ml-auto">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name…"
                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm text-stone-800 focus:outline-none focus:ring-2 focus:ring-brand-300 w-48">
                <button type="submit"
                    class="rounded-lg bg-stone-100 px-3 py-1.5 text-sm font-medium text-stone-700 hover:bg-stone-200 transition">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.products.index', ['status' => $status]) }}"
                       class="rounded-lg bg-stone-100 px-3 py-1.5 text-sm font-medium text-stone-500 hover:bg-stone-200 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        @if ($products->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-stone-200">
                <p class="text-stone-500">No products found.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 border-b border-stone-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Product</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden sm:table-cell">Farmer</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden md:table-cell">Category</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Price</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($products as $product)
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-stone-800">{{ $product->name }}</p>
                                    <p class="text-xs text-stone-400 mt-0.5">{{ $product->short_description }}</p>
                                </td>
                                <td class="px-4 py-3 text-stone-600 hidden sm:table-cell">
                                    <p>{{ $product->farmerProfile->farm_name }}</p>
                                    <p class="text-xs text-stone-400">{{ $product->farmerProfile->user->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-stone-500 hidden md:table-cell">
                                    {{ $product->category->name }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-stone-700">
                                    {{ $product->priceLabel() }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($product->removed_at)
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Removed</span>
                                    @elseif (! $product->is_available)
                                        <span class="inline-flex items-center rounded-full bg-stone-100 px-2 py-0.5 text-xs font-semibold text-stone-500">Unavailable</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($product->removed_at)
                                        <form method="POST" action="{{ route('admin.products.restore', $product) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs font-medium text-brand-600 hover:text-brand-800 transition">
                                                Restore
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.products.remove', $product) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Remove \"{{ addslashes($product->name) }}\" from the marketplace?')"
                                                class="text-xs font-medium text-red-600 hover:text-red-800 transition">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.dashboard>
