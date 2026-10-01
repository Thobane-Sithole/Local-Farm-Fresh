<x-layouts.dashboard title="Users" area="admin">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Users</h1>
            <p class="mt-1 text-muted">All registered accounts on the platform.</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="flex flex-wrap gap-1 rounded-xl bg-line/40 p-1">
            @php
                $tabs = ['all' => 'All', 'customer' => 'Customers', 'farmer' => 'Farmers', 'admin' => 'Admins'];
                $currentRole = request('role', 'all');
            @endphp
            @foreach ($tabs as $value => $label)
                <a href="{{ route('admin.users.index', array_merge(request()->except('page'), ['role' => $value === 'all' ? null : $value])) }}"
                   @class([
                       'rounded-lg px-3 py-1.5 text-sm font-semibold transition-colors',
                       'bg-white text-ink shadow-sm' => $currentRole === $value || ($value === 'all' && ! request('role')),
                       'text-muted hover:text-ink' => ! ($currentRole === $value || ($value === 'all' && ! request('role'))),
                   ])>
                    {{ $label }}
                    <span class="ml-1 text-xs text-muted">({{ $counts[$value] }})</span>
                </a>
            @endforeach
        </div>

        <div class="relative ml-auto w-full sm:w-72">
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Search name or email…"
                   class="block w-full rounded-xl border border-line bg-white py-2 pl-9 pr-3 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/></svg>
        </div>
    </form>

    @if ($users->isEmpty())
        <x-ui.empty-state title="No users found" class="mt-8">
            Try adjusting your search or filter.
        </x-ui.empty-state>
    @else
        <div class="mt-4 overflow-hidden rounded-card bg-white shadow-card ring-1 ring-line/60">
            <table class="min-w-full divide-y divide-line">
                <thead>
                    <tr class="bg-brand-50">
                        <th scope="col" class="py-3.5 pl-5 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-muted">Name</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted sm:table-cell">Role</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted">Status</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted md:table-cell">Registered</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-5"><span class="sr-only">Edit</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($users as $user)
                        <tr @class(['bg-red-50/50' => $user->isSuspended()])>
                            <td class="py-3.5 pl-5 pr-3">
                                <p class="text-sm font-semibold text-ink">{{ $user->name }}</p>
                                <p class="text-xs text-muted">{{ $user->email }}</p>
                            </td>
                            <td class="hidden px-3 py-3.5 sm:table-cell">
                                @php
                                    $roleColour = match ($user->role) {
                                        \App\Enums\UserRole::Admin => 'bg-purple-100 text-purple-800',
                                        \App\Enums\UserRole::Farmer => 'bg-brand-100 text-brand-800',
                                        default => 'bg-line text-muted',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $roleColour }}">{{ $user->role->label() }}</span>
                            </td>
                            <td class="px-3 py-3.5">
                                @if ($user->isSuspended())
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Suspended</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-800">Active</span>
                                @endif
                            </td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted md:table-cell">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-3.5 pl-3 pr-5 text-right text-sm">
                                <a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-brand-600 hover:text-brand-800">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</x-layouts.dashboard>
