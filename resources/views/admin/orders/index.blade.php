<x-layouts.dashboard title="All Orders" area="admin">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-brand-900">All Orders</h1>
            <div class="flex items-center gap-2 text-sm">
                <span class="bg-stone-100 text-stone-600 font-semibold px-2.5 py-0.5 rounded-full">
                    {{ $counts['all'] }} total
                </span>
                @if ($counts['pending'] > 0)
                    <span class="bg-amber-100 text-amber-700 font-semibold px-2.5 py-0.5 rounded-full">
                        {{ $counts['pending'] }} pending
                    </span>
                @endif
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-5">
            {{-- Status tabs --}}
            <div class="flex items-center gap-1 overflow-x-auto pb-1">
                @php
                    $tabs = [
                        ''                   => 'All',
                        'pending'            => 'Pending',
                        'confirmed'          => 'Confirmed',
                        'preparing'          => 'Preparing',
                        'ready_for_delivery' => 'Ready',
                        'out_for_delivery'   => 'Out for delivery',
                        'delivered'          => 'Delivered',
                        'cancelled'          => 'Cancelled',
                    ];
                    $activeStatus = request('status', '');
                @endphp
                @foreach ($tabs as $val => $label)
                    <a href="{{ route('admin.orders.index', array_merge(request()->only('search'), $val ? ['status' => $val] : [])) }}"
                       class="whitespace-nowrap rounded-full px-3 py-1 text-sm font-medium transition
                           {{ $activeStatus === $val
                               ? 'bg-brand-600 text-white'
                               : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-2 ml-auto">
                @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Order # or customer…"
                       class="rounded-lg border border-stone-300 px-3 py-1.5 text-sm text-stone-800 focus:outline-none focus:ring-2 focus:ring-brand-300 w-48">
                <button type="submit"
                    class="rounded-lg bg-stone-100 px-3 py-1.5 text-sm font-medium text-stone-700 hover:bg-stone-200 transition">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.orders.index', request()->only('status')) }}"
                       class="rounded-lg bg-stone-100 px-3 py-1.5 text-sm font-medium text-stone-500 hover:bg-stone-200 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        @if ($orders->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-stone-200">
                <p class="text-stone-500">No orders found.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-stone-50 border-b border-stone-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Order</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden sm:table-cell">Customer</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden md:table-cell">Farmer</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide hidden sm:table-cell">Date</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Total</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($orders as $order)
                            @php
                                $statusColors = [
                                    'pending'            => 'bg-amber-100 text-amber-700',
                                    'confirmed'          => 'bg-blue-100 text-blue-700',
                                    'preparing'          => 'bg-indigo-100 text-indigo-700',
                                    'ready_for_delivery' => 'bg-purple-100 text-purple-700',
                                    'out_for_delivery'   => 'bg-cyan-100 text-cyan-700',
                                    'delivered'          => 'bg-green-100 text-green-700',
                                    'cancelled'          => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="px-4 py-3 font-mono text-xs text-stone-700">{{ $order->order_number }}</td>
                                <td class="px-4 py-3 text-stone-700 hidden sm:table-cell">{{ $order->customer->name }}</td>
                                <td class="px-4 py-3 text-stone-500 hidden md:table-cell">{{ $order->farmerProfile->farm_name }}</td>
                                <td class="px-4 py-3 text-stone-500 hidden sm:table-cell">{{ $order->placed_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-stone-800">R{{ number_format((float)$order->total, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusColors[$order->status->value] ?? '' }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="text-brand-600 hover:text-brand-800 font-medium text-xs">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-layouts.dashboard>
