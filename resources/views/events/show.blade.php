<x-layouts.site :title="$event->title">
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">

        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-800 mb-6">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back
        </a>

        {{-- Poster --}}
        @if ($event->poster_url)
            <div class="mb-6 overflow-hidden rounded-2xl shadow-md ring-1 ring-line/60">
                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" class="w-full object-cover max-h-80">
            </div>
        @endif

        <article class="rounded-card bg-white p-6 shadow-card ring-1 ring-line/60">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink">{{ $event->title }}</h1>

            <div class="mt-3 flex flex-wrap gap-4 text-sm text-muted">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    {{ $event->starts_at->format('l, d F Y') }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    {{ $event->starts_at->format('H:i') }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    {{ $event->location }}
                </span>
            </div>

            @if ($event->description)
                <p class="mt-4 text-sm/relaxed text-ink whitespace-pre-line">{{ $event->description }}</p>
            @endif
        </article>

        {{-- Comments --}}
        <section class="mt-6" aria-label="RSVP & comments">
            <h2 class="text-base font-bold text-ink mb-3">{{ count($comments) }} {{ Str::plural('Comment', count($comments)) }}</h2>

            @if (count($comments) > 0)
                <div class="space-y-3">
                    @foreach ($comments as $comment)
                        <div class="flex gap-3 rounded-card bg-white p-4 shadow-card ring-1 ring-line/60">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-ink">{{ $comment->user->username ?? $comment->user->name }}</span>
                                    <span class="text-xs text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm text-ink">{{ $comment->body }}</p>
                            </div>
                            @if (auth()->check() && (auth()->user()->isAdmin() || $comment->user_id === auth()->id()))
                                <form method="POST" action="{{ route('events.comments.destroy', $comment) }}"
                                      onsubmit="return confirm('Delete this comment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="shrink-0 rounded-lg p-1 text-muted hover:text-red-600 transition-colors" aria-label="Delete comment">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @auth
                <div class="mt-5 rounded-card bg-white p-5 shadow-card ring-1 ring-line/60">
                    <form method="POST" action="{{ route('events.comments.store', $event) }}">
                        @csrf
                        <label for="event-comment" class="sr-only">Leave a comment</label>
                        <textarea
                            id="event-comment"
                            name="body"
                            rows="2"
                            placeholder="Are you going? Leave a comment…"
                            maxlength="500"
                            class="block w-full resize-none rounded-xl border border-line bg-white p-3 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('body') border-red-400 @enderror"
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex justify-end">
                            <x-ui.button type="submit" size="sm">Comment</x-ui.button>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-5 rounded-card border border-brand-100 bg-brand-50 p-4 text-center text-sm text-muted">
                    <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-800">Log in</a> to leave a comment.
                </div>
            @endauth
        </section>
    </div>
</x-layouts.site>
