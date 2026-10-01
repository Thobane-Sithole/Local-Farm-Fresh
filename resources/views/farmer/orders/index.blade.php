<x-layouts.dashboard title="Orders">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-brand-900">Orders</h1>
            <div class="flex items-center gap-2 text-sm">
                <span class="bg-amber-100 text-amber-700 font-semibold px-2.5 py-0.5 rounded-full">
                    {{ $counts['pending'] }} pending
                </span>
            </div>
        </div>

        {{-- Status filter tabs --}}
        <div class="flex items-center gap-1 mb-5 overflow-x-auto pb-1">
            @php
                $tabs = [
                    ''        => ['All', $counts['all']],
                    'pending' => ['Pending', null],
                    'confirmed' => ['Confirmed', null],
                    'preparing' => ['Preparing', null],
                    'ready_for_delivery' => ['Ready', null],
                    'out_for_delivery'   => ['Out for delivery', null],
                    'delivered'          => ['Delivered', null],
                    'cancelled'          => ['Cancelled', null],
                ];
                $active = request('status', '');
            @endphp
            @foreach ($tabs as $val => [$label, $count])
                <a href="{{ route('farmer.orders.index', $val ? ['status' => $val] : []) }}"
                   class="whitespace-nowrap rounded-full px-3 py-1 text-sm font-medium transition
                       {{ $active === $val
                           ? 'bg-brand-600 text-white'
                           : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                    {{ $label }}{{ $count !== null ? " ($count)" : '' }}
                </a>
            @endforeach
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
                            <th class="text-left px-4 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wide">Customer</th>
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
                                <td class="px-4 py-3 text-stone-700">{{ $order->customer->name }}</td>
                                <td class="px-4 py-3 text-stone-500 hidden sm:table-cell">{{ $order->placed_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-stone-800">R{{ number_format((float)$order->total, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusColors[$order->status->value] ?? '' }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('farmer.orders.show', $order->order_number) }}"
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
