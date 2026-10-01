<x-layouts.site title="Our farmers" description="Meet the small-scale South African farmers selling fresh produce on Local-Farm-Fresh.">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Our farmers</h1>
        <p class="mt-1 text-muted">{{ $farmers->total() }} farms selling fresh produce near you.</p>

        @if ($farmers->isEmpty())
            <x-ui.empty-state title="No farms listed yet" class="mt-8">
                Check back soon — more farmers are joining every day.
            </x-ui.empty-state>
        @else
            <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" role="list">
                @foreach ($farmers as $farmer)
                    <li>
                        <a href="{{ route('farmers.show', $farmer) }}"
                           class="group flex flex-col gap-3 rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 transition-shadow hover:shadow-md">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-ink group-hover:text-brand-700">{{ $farmer->farm_name }}</p>
                                    <p class="text-sm text-muted">{{ $farmer->locationLabel() }}</p>
                                </div>
                            </div>

                            @if ($farmer->description)
                                <p class="line-clamp-2 text-sm text-muted">{{ $farmer->description }}</p>
                            @endif

                            <div class="flex items-center justify-between border-t border-line pt-3">
                                <span class="text-sm text-muted">
                                    {{ trans_choice(':count product|:count products', $farmer->products_count) }}
                                </span>
                                <span class="text-sm font-semibold text-brand-600 group-hover:text-brand-800">
                                    View farm →
                                </span>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-8">
                {{ $farmers->links() }}
            </div>
        @endif
    </div>
</x-layouts.site>
