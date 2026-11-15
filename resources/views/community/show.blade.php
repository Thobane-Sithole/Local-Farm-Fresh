<x-layouts.site title="Community post">
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8"
         x-data="commentThread()"
         x-init="init()">

        <a href="{{ route('community.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-800 mb-6">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Community Hub
        </a>

        {{-- Post --}}
        <article class="rounded-card bg-white p-6 shadow-card ring-1 ring-line/60">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <span class="text-sm font-bold text-ink">@<span>{{ $post->user->username ?? $post->user->name }}</span></span>
                    <span class="ml-2 text-xs text-muted">{{ $post->created_at->diffForHumans() }}</span>
                </div>
                @if (auth()->check() && (auth()->user()->isAdmin() || $post->user_id === auth()->id()))
                    <form method="POST" action="{{ route('community.destroy', $post) }}"
                          onsubmit="return confirm('Delete this post and all its comments?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="shrink-0 rounded-lg p-1 text-muted hover:text-red-600 transition-colors" aria-label="Delete post">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                    </form>
                @endif
            </div>
            <p class="mt-3 whitespace-pre-line text-sm text-ink">{{ $post->body }}</p>
        </article>

        {{-- Comments section --}}
        <section class="mt-6" aria-label="Comments">

            {{-- Count updates reactively --}}
            <h2 class="text-base font-bold text-ink mb-3">
                <span x-text="totalCount"></span>
                <span x-text="totalCount === 1 ? 'Comment' : 'Comments'"></span>
            </h2>

            {{-- Server-rendered existing comments --}}
            @if ($comments->isNotEmpty())
                <div class="space-y-3" id="existing-comments">
                    @foreach ($comments as $comment)
                        <div class="flex gap-3 rounded-card bg-white p-4 shadow-card ring-1 ring-line/60"
                             id="comment-{{ $comment->id }}">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-ink">@<span>{{ $comment->user->username ?? $comment->user->name }}</span></span>
                                    <span class="text-xs text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm text-ink">{{ $comment->body }}</p>
                            </div>
                            @if (auth()->check() && (auth()->user()->isAdmin() || $comment->user_id === auth()->id()))
                                <button type="button"
                                        @click="deleteExisting('{{ $comment->id }}', '{{ route('community.comments.destroy', $comment) }}')"
                                        class="shrink-0 rounded-lg p-1 text-muted hover:text-red-600 transition-colors" aria-label="Delete comment">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- New comments injected via Alpine appear below existing ones --}}
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
                                <span class="text-sm font-bold text-ink">@<span x-text="comment.username"></span></span>
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

            {{-- Comment form --}}
            @auth
                <div class="mt-5 rounded-card bg-white p-5 shadow-card ring-1 ring-line/60">
                    <form @submit.prevent="submitComment">
                        <label for="comment-body" class="sr-only">Write a comment</label>
                        <textarea
                            id="comment-body"
                            x-model="body"
                            rows="2"
                            placeholder="Write a comment…"
                            maxlength="1000"
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
    function commentThread() {
        return {
            body: '',
            submitting: false,
            error: '',
            liveComments: [],
            serverCount: {{ $comments->count() }},

            get totalCount() {
                return this.serverCount + this.liveComments.length;
            },

            init() {},

            async submitComment() {
                if (!this.body.trim() || this.submitting) return;
                this.submitting = true;
                this.error = '';

                try {
                    const res = await fetch('{{ route('community.comments.store', $post) }}', {
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
                const el = document.getElementById('comment-' + id);

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
