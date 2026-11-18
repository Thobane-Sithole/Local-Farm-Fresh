<x-layouts.site title="Our farmers" description="Meet the small-scale South African farmers selling fresh produce on Local-Farm-Fresh.">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Our farmers</h1>
                <p class="mt-1 text-muted">
                    @if ($hasLocation)
                        {{ $farmers->total() }} {{ Str::plural('farm', $farmers->total()) }} within 5 km of your location.
                    @else
                        {{ $farmers->total() }} farms selling fresh produce.
                    @endif
                </p>
            </div>

            @if ($hasLocation)
                <form method="POST" action="{{ route('location.clear') }}" class="shrink-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-white px-3 py-2 text-sm font-semibold text-muted hover:text-ink transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        Within 5 km · Clear
                    </button>
                </form>
            @endif
        </div>

        {{-- Location prompt --}}
        @if (! $hasLocation)
            <div class="mt-6 rounded-xl border border-brand-200 bg-brand-50 p-4"
                 x-data="{ loading: false, error: '' }"
                 id="location-prompt">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold text-brand-900">Find farms near you</p>
                        <p class="mt-0.5 text-sm text-brand-700">Allow your location to see only farms within 5 km.</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <form id="location-form" method="POST" action="{{ route('location.store') }}">
                                @csrf
                                <input type="hidden" name="latitude"  id="loc-lat">
                                <input type="hidden" name="longitude" id="loc-lng">
                            </form>
                            <button type="button"
                                @click="
                                    loading = true; error = '';
                                    if (!navigator.geolocation) { error = 'Your browser does not support geolocation.'; loading = false; return; }
                                    navigator.geolocation.getCurrentPosition(
                                        (pos) => {
                                            document.getElementById('loc-lat').value = pos.coords.latitude;
                                            document.getElementById('loc-lng').value = pos.coords.longitude;
                                            document.getElementById('location-form').submit();
                                        },
                                        (err) => { error = 'Could not get your location. Please allow location access and try again.'; loading = false; }
                                    );
                                "
                                :disabled="loading"
                                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-60 transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <span x-text="loading ? 'Getting location…' : 'Allow location'"></span>
                            </button>
                        </div>
                        <p x-show="error" x-cloak x-text="error" class="mt-2 text-sm font-medium text-red-700"></p>
                    </div>
                </div>
            </div>
        @endif

        @if ($farmers->isEmpty())
            <div class="mt-8 rounded-2xl border border-stone-200 bg-white py-20 text-center">
                <svg class="mx-auto h-12 w-12 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                @if ($hasLocation)
                    <p class="mt-4 text-stone-600 font-semibold">No farms found within 5 km</p>
                    <p class="mt-1 text-sm text-stone-400">There are no farms with active listings near your location yet.</p>
                    <form method="POST" action="{{ route('location.clear') }}" class="mt-5 inline-block">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
                            Browse all farms
                        </button>
                    </form>
                @else
                    <p class="mt-4 text-stone-500">No farms listed yet — check back soon.</p>
                @endif
            </div>
        @else
            <ul class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3" role="list">
                @foreach ($farmers as $farmer)
                    <li>
                        <a href="{{ route('farmers.show', $farmer) }}"
                           class="group flex flex-col gap-3 rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 transition-shadow hover:shadow-md">
                            <div class="flex items-center gap-3">
                                @if ($farmer->profile_image_url)
                                    <img src="{{ $farmer->profile_image_url }}" alt="{{ $farmer->farm_name }}"
                                         class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-line">
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 9a7 7 0 0 1 14 0"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-ink group-hover:text-brand-700">{{ $farmer->farm_name }}</p>
                                    <p class="text-sm text-muted">{{ $farmer->locationLabel() }}</p>
                                </div>
                                @if ($hasLocation && isset($farmer->distance_km))
                                    <span class="shrink-0 rounded-full bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-700">
                                        {{ number_format($farmer->distance_km, 1) }} km
                                    </span>
                                @endif
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
