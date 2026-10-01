<x-layouts.dashboard title="Analytics" area="farmer">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight">Analytics</h1>
        <a href="{{ route('farmer.dashboard') }}" class="text-sm text-muted hover:text-ink">← Dashboard</a>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-8">
        <div class="rounded-2xl bg-white border border-line p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-muted">Total revenue</p>
            <p class="mt-1 text-2xl font-extrabold text-ink">R{{ number_format((float)$totalRevenue, 0) }}</p>
            <p class="text-xs text-muted mt-0.5">delivered orders</p>
        </div>
        <div class="rounded-2xl bg-white border border-line p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-muted">Avg. rating</p>
            <p class="mt-1 text-2xl font-extrabold text-ink">
                {{ $reviewCount > 0 ? number_format($avgRating, 1) : '—' }}
                @if ($reviewCount > 0)
                    <span class="text-amber-400 text-xl">★</span>
                @endif
            </p>
            <p class="text-xs text-muted mt-0.5">{{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}</p>
        </div>
        @foreach ([
            \App\Enums\OrderStatus::Pending->value   => 'Pending',
            \App\Enums\OrderStatus::Confirmed->value => 'Confirmed',
        ] as $status => $label)
            <div class="rounded-2xl bg-white border border-line p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $label }}</p>
                <p class="mt-1 text-2xl font-extrabold text-ink">{{ $statusCounts[$status] ?? 0 }}</p>
                <p class="text-xs text-muted mt-0.5">orders</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Revenue chart --}}
        <div class="rounded-2xl bg-white border border-line p-5">
            <h2 class="text-sm font-semibold text-ink mb-4">Revenue (last 6 months)</h2>
            @if ($monthly->isEmpty())
                <p class="text-sm text-muted py-8 text-center">No orders in the last 6 months.</p>
            @else
                <canvas id="revenueChart" height="200"></canvas>
            @endif
        </div>

        {{-- Top products --}}
        <div class="rounded-2xl bg-white border border-line p-5">
            <h2 class="text-sm font-semibold text-ink mb-4">Top products</h2>
            @if ($topProducts->isEmpty())
                <p class="text-sm text-muted py-8 text-center">No delivered orders yet.</p>
            @else
                <ol class="space-y-3">
                    @foreach ($topProducts as $product)
                        <li class="flex items-center gap-3">
                            <span class="shrink-0 w-5 text-xs text-muted text-right">{{ $loop->iteration }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-ink truncate">{{ $product->product_name }}</p>
                                <p class="text-xs text-muted">{{ number_format($product->units) }} units sold</p>
                            </div>
                            <span class="text-sm font-semibold text-ink shrink-0">R{{ number_format($product->revenue, 0) }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    {{-- Order count chart --}}
    @if ($monthly->isNotEmpty())
        <div class="mt-6 rounded-2xl bg-white border border-line p-5">
            <h2 class="text-sm font-semibold text-ink mb-4">Orders per month</h2>
            <canvas id="ordersChart" height="120"></canvas>
        </div>
    @endif

    @if ($monthly->isNotEmpty())
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const labels  = @json($monthly->pluck('month'));
            const revenue = @json($monthly->pluck('revenue')->map(fn ($v) => round((float)$v, 2)));
            const orders  = @json($monthly->pluck('orders')->map(fn ($v) => (int)$v));

            const green = '#23823F';
            const greenLight = 'rgba(35,130,63,0.12)';

            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Revenue (R)',
                        data: revenue,
                        borderColor: green,
                        backgroundColor: greenLight,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: green,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => 'R' + v.toLocaleString() } },
                        x: { grid: { display: false } },
                    },
                },
            });

            new Chart(document.getElementById('ordersChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Orders',
                        data: orders,
                        backgroundColor: greenLight,
                        borderColor: green,
                        borderWidth: 2,
                        borderRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } },
                    },
                },
            });
        })();
        </script>
        @endpush
    @endif
</x-layouts.dashboard>
