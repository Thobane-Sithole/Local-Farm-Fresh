<x-layouts.dashboard title="Admin dashboard" area="admin">
    <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Platform overview</h1>
    <p class="mt-1 text-muted">Everything happening on Local-Farm-Fresh.</p>

    <div class="mt-8 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
        <x-dashboard.stat-card label="Pending orders" :value="$stats['pending_orders']" :highlight="$stats['pending_orders'] > 0" />
        <x-dashboard.stat-card label="Completed orders" :value="$stats['completed_orders']" />
        <x-dashboard.stat-card label="Total orders" :value="$stats['orders']" />
        <x-dashboard.stat-card label="Farmers" :value="$stats['farmers']" />
        <x-dashboard.stat-card label="Customers" :value="$stats['customers']" />
        <x-dashboard.stat-card label="Active products" :value="$stats['products']" />
        @if ($stats['unverified_farmers'] > 0)
            <x-dashboard.stat-card label="Unverified farms" :value="$stats['unverified_farmers']" highlight />
        @endif
        @if ($stats['removed_products'] > 0)
            <x-dashboard.stat-card label="Removed products" :value="$stats['removed_products']" />
        @endif
    </div>
</x-layouts.dashboard>
