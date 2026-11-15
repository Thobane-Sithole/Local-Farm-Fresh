<x-layouts.site :title="$event->title">
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8"
         x-data="eventComments()"
         x-init="init()">

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
            <h2 class="text-base font-bold text-ink mb-3">
                <span x-text="totalCount"></span>
                <span x-text="totalCount === 1 ? 'Comment' : 'Comments'"></span>
            </h2>

            {{-- Server-rendered existing comments --}}
            @if (count($comments) > 0)
                <div class="space-y-3" id="existing-event-comments">
                    @foreach ($comments as $comment)
                        <div class="flex gap-3 rounded-card bg-white p-4 shadow-card ring-1 ring-line/60"
                             id="evcomment-{{ $comment->id }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-ink">{{ $comment->user->username ?? $comment->user->name }}</span>
                                    <span class="text-xs text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm text-ink">{{ $comment->body }}</p>
                            </div>
                            @if (auth()->check() && (auth()->user()->isAdmin() || $comment->user_id === auth()->id()))
                                <button type="button"
                                        @click="deleteExisting('{{ $comment->id }}', '{{ route('events.comments.destroy', $comment) }}')"
                                        class="shrink-0 rounded-lg p-1 text-muted hover:text-red-600 transition-colors" aria-label="Delete comment">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Live comments injected via Alpine --}}
            <div class="space-y-3 mt-3">
                <template x-for="comment in liveComments" :key="comment.id">
                    <div
                        x-show="true"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        class="flex gap-3 rounded-card bg-white p-4 shadow-card ring-1 ring-brand-200/60">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-ink" x-text="comment.username"></span>
                                <span class="text-xs text-muted">· just now</span>
                            </div>
                            <p class="mt-1 text-sm text-ink" x-text="comment.body"></p>
                        </div>
                        <button type="button"
                                @click="deleteLive(comment)"
                                class="shrink-0 rounded-lg p-1 text-muted hover:text-red-600 transition-colors" aria-label="Delete comment">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            @auth
                <div class="mt-5 rounded-card bg-white p-5 shadow-card ring-1 ring-line/60">
                    <form @submit.prevent="submitComment">
                        <label for="event-comment" class="sr-only">Leave a comment</label>
                        <textarea
                            id="event-comment"
                            x-model="body"
                            rows="2"
                            placeholder="Are you going? Leave a comment…"
                            maxlength="500"
                            :disabled="submitting"
                            class="block w-full resize-none rounded-xl border border-line bg-white p-3 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 disabled:opacity-60"
                        ></textarea>
                        <p x-show="error" x-text="error" class="mt-1 text-xs text-red-600"></p>
                        <div class="mt-2 flex justify-end">
                            <button type="submit"
                                    :disabled="submitting || !body.trim()"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="submitting" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                <span x-text="submitting ? 'Posting…' : 'Comment'"></span>
                            </button>
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

    <script>
    function eventComments() {
        return {
            body: '',
            submitting: false,
            error: '',
            liveComments: [],
            serverCount: {{ count($comments) }},

            get totalCount() {
                return this.serverCount + this.liveComments.length;
            },

            init() {},

            async submitComment() {
                if (!this.body.trim() || this.submitting) return;
                this.submitting = true;
                this.error = '';

                try {
                    const res = await fetch('{{ route('events.comments.store', $event) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({ body: this.body }),
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        this.error = data.errors?.body?.[0] ?? 'Something went wrong.';
                        return;
                    }

                    this.liveComments.push(data.comment);
                    this.body = '';
                } catch {
                    this.error = 'Could not connect. Please try again.';
                } finally {
                    this.submitting = false;
                }
            },

            async deleteExisting(id, url) {
                if (!confirm('Delete this comment?')) return;
                const el = document.getElementById('evcomment-' + id);
                try {
                    const fd = new FormData();
                    fd.append('_method', 'DELETE');
                    fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                    await fetch(url, { method: 'POST', body: fd });
                    if (el) {
                        el.style.transition = 'opacity 200ms, transform 200ms';
                        el.style.opacity = '0';
                        el.style.transform = 'scale(0.97)';
                        setTimeout(() => el.remove(), 200);
                    }
                    this.serverCount = Math.max(0, this.serverCount - 1);
                } catch {
                    alert('Could not delete comment.');
                }
            },

            async deleteLive(comment) {
                if (!confirm('Delete this comment?')) return;
                try {
                    const fd = new FormData();
                    fd.append('_method', 'DELETE');
                    fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                    await fetch(comment.delete_url, { method: 'POST', body: fd });
                    this.liveComments = this.liveComments.filter(c => c.id !== comment.id);
                } catch {
                    alert('Could not delete comment.');
                }
            },
        };
    }
    </script>
</x-layouts.site>
