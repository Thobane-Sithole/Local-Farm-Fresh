<x-layouts.dashboard title="Events" area="admin">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Events</h1>
            <p class="mt-1 text-muted">Farmers market events and announcements.</p>
        </div>
        <x-ui.button :href="route('admin.events.create')" size="sm">New event</x-ui.button>
    </div>

    @if ($events->isEmpty())
        <x-ui.empty-state title="No events yet" class="mt-8">
            Create your first farmers market event.
        </x-ui.empty-state>
    @else
        <div class="mt-6 overflow-hidden rounded-card bg-white shadow-card ring-1 ring-line/60">
            <table class="min-w-full divide-y divide-line">
                <thead>
                    <tr class="bg-brand-50">
                        <th scope="col" class="py-3.5 pl-5 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-muted">Event</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted sm:table-cell">Date</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted md:table-cell">Location</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-5"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($events as $event)
                        <tr @class(['opacity-60' => $event->starts_at->isPast()])>
                            <td class="py-3.5 pl-5 pr-3">
                                <p class="text-sm font-semibold text-ink">{{ $event->title }}</p>
                                @if ($event->starts_at->isPast())
                                    <span class="text-xs text-muted">Past</span>
                                @else
                                    <span class="text-xs text-brand-600 font-semibold">Upcoming</span>
                                @endif
                            </td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted sm:table-cell">
                                {{ $event->starts_at->format('d M Y, H:i') }}
                            </td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted md:table-cell">
                                {{ $event->location }}
                            </td>
                            <td class="py-3.5 pl-3 pr-5 text-right text-sm">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.events.edit', $event) }}" class="font-semibold text-brand-600 hover:text-brand-800">Edit</a>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                          onsubmit="return confirm('Delete {{ addslashes($event->title) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</x-layouts.dashboard>
