<x-layouts.dashboard title="Farmers" area="admin">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-brand-900">Farmers</h1>
            <div class="flex items-center gap-2 text-sm">
                <span class="bg-stone-100 text-stone-600 font-semibold px-2.5 py-0.5 rounded-full">
                    {{ $counts['total'] }} total
                </span>
                <span class="bg-green-100 text-green-700 font-semibold px-2.5 py-0.5 rounded-full">
                    {{ $counts['verified'] }} verified
                </span>
                @if ($counts['unverified'] > 0)
                    <span class="bg-amber-100 text-amber-700 font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $counts['unverified'] }} pending
                    </span>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($farmers->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-stone-200">
                <p class="text-stone-500">No farmer profiles found.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 border-b border-stone-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Farm</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden sm:table-cell">Owner</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden md:table-cell">Location</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Products</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Orders</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Verified</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($farmers as $farmer)
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-stone-800">{{ $farmer->farm_name }}</p>
                                </td>
                                <td class="px-4 py-3 text-stone-600 hidden sm:table-cell">
                                    <p>{{ $farmer->user->name }}</p>
                                    <p class="text-xs text-stone-400">{{ $farmer->user->email }}</p>
                                </td>
                                <td class="px-4 py-3 text-stone-500 hidden md:table-cell">
                                    {{ $farmer->locationLabel() }}
                                </td>
                                <td class="px-4 py-3 text-center text-stone-600">{{ $farmer->products_count }}</td>
                                <td class="px-4 py-3 text-center text-stone-600">{{ $farmer->orders_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($farmer->is_verified)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Verified</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($farmer->is_verified)
                                        <form method="POST" action="{{ route('admin.farmers.unverify', $farmer) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                onclick="return confirm('Remove verification from {{ addslashes($farmer->farm_name) }}?')"
                                                class="text-xs font-medium text-red-600 hover:text-red-800 transition">
                                                Unverify
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.farmers.verify', $farmer) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs font-medium text-brand-600 hover:text-brand-800 transition">
                                                Verify
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
                {{ $farmers->links() }}
            </div>
        @endif
    </div>
</x-layouts.dashboard>
