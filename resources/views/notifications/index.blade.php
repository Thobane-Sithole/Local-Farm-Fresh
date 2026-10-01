<x-layouts.site title="Notifications">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
        <h1 class="text-2xl font-bold text-brand-900 mb-6">Notifications</h1>

        @if ($notifications->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-stone-200">
                <svg class="mx-auto h-12 w-12 text-stone-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
                <p class="text-stone-500">No notifications yet.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $icon = match($data['type'] ?? '') {
                            'order_placed'        => ['bg-green-100 text-green-600', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            'new_order'           => ['bg-brand-100 text-brand-600', 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z'],
                            'order_status_changed'=> ['bg-blue-100 text-blue-600', 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12'],
                            default               => ['bg-stone-100 text-stone-500', 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
                        };
                    @endphp
                    <div class="flex items-start gap-4 rounded-xl border border-stone-200 bg-white px-4 py-3
                        {{ $notification->read_at ? '' : 'border-brand-200 bg-brand-50/40' }}">
                        <div class="mt-0.5 flex-shrink-0 h-9 w-9 rounded-full {{ $icon[0] }} flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $icon[1] }}" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-stone-800 {{ $notification->read_at ? '' : 'font-semibold' }}">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <p class="text-xs text-stone-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if (isset($data['url']))
                                <a href="{{ $data['url'] }}" class="text-xs font-medium text-brand-600 hover:text-brand-800">View →</a>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-stone-300 hover:text-red-400 transition" title="Dismiss">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $notifications->links() }}</div>
        @endif
    </div>
</x-layouts.site>
