<x-layouts.site title="Community Hub">
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8"
         x-data="communityFeed()"
         x-init="init()">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink">Community Hub</h1>
            <p class="mt-1 text-muted">Share recipes, tips and stories with the Local Farm Fresh community.</p>
        </div>

        @auth
            @if (auth()->user()->hasUsername())
                {{-- Post form — Alpine handles submit, no page load --}}
                <div class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 mb-6">
                    <form @submit.prevent="submitPost">
                        <label for="body" class="sr-only">What's on your mind?</label>
                        <textarea
                            id="body"
                            x-model="body"
                            rows="3"
                            placeholder="Share a recipe, tip, or story…"
                            maxlength="2000"
                            :disabled="submitting"
                            class="block w-full resize-none rounded-xl border border-line bg-white p-3 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 disabled:opacity-60"
                        ></textarea>
                        <p x-show="error" x-text="error" class="mt-1 text-xs text-red-600"></p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs text-muted">
                                Posting as <strong class="text-ink">@<span>{{ auth()->user()->username }}</span></strong>
                            </span>
                            <button type="submit"
                                    :disabled="submitting || !body.trim()"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="submitting" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                <span x-text="submitting ? 'Posting…' : 'Post'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="rounded-card border border-brand-100 bg-brand-50 p-5 mb-8 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-ink">Choose a username to start posting</p>
                        <p class="text-sm text-muted mt-0.5">Pick a community name and join the conversation.</p>
                    </div>
                    <x-ui.button :href="route('username.edit')" size="sm">Set username</x-ui.button>
                </div>
            @endif
        @endauth

        {{-- Live posts injected via Alpine sit above the server-rendered feed --}}
        <div class="space-y-4 mb-4">
            <template x-for="post in livePosts" :key="post.id">
                <article
                    x-show="true"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-3 scale-[0.98]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    class="rounded-card bg-white p-5 shadow-card ring-1 ring-brand-200/60">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-ink">@<span x-text="post.username"></span></span>
                                <span class="text-xs text-muted">· just now</span>
                            </div>
                            <p class="mt-2 whitespace-pre-line text-sm text-ink" x-text="post.body"></p>
                        </div>
                        <form :action="post.delete_url" method="POST" @submit.prevent="deletePost(post, $event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="shrink-0 rounded-lg p-1 text-muted hover:text-red-600 transition-colors" aria-label="Delete post">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </form>
                    </div>
                    <div class="mt-3 border-t border-line/50 pt-3">
                        <a :href="post.show_url" class="text-xs font-semibold text-brand-600 hover:text-brand-800">0 comments · View post →</a>
                    </div>
                </article>
            </template>
        </div>

        {{-- Server-rendered existing posts --}}
        @if ($posts->isEmpty())
            <div x-show="livePosts.length === 0" class="rounded-card bg-white p-12 text-center shadow-card ring-1 ring-line/60">
                <svg class="mx-auto h-10 w-10 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
                <p class="mt-3 text-sm font-semibold text-ink">No posts yet</p>
                <p class="text-sm text-muted">Be the first to share something!</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($posts as $post)
                    <article class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-ink">@<span>{{ $post->user->username ?? $post->user->name }}</span></span>
                                    <span class="text-xs text-muted">· {{ $post->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-2 whitespace-pre-line text-sm text-ink">{{ Str::limit($post->body, 300) }}</p>
                            </div>
                            @if (auth()->check() && (auth()->user()->isAdmin() || $post->user_id === auth()->id()))
                                <form method="POST" action="{{ route('community.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="shrink-0 rounded-lg p-1 text-muted hover:text-red-600 transition-colors" aria-label="Delete post">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center gap-4 border-t border-line/50 pt-3">
                            <a href="{{ route('community.show', $post) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">
                                {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }} · View post →
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        @endif

        @guest
            <div class="mt-8 rounded-card border border-brand-100 bg-brand-50 p-5 text-center">
                <p class="text-sm font-semibold text-ink">Want to join the conversation?</p>
                <p class="mt-1 text-sm text-muted">Create an account to share recipes and tips.</p>
                <div class="mt-3 flex justify-center gap-2">
                    <x-ui.button :href="route('register')" size="sm">Sign up</x-ui.button>
                    <x-ui.button :href="route('login')" variant="secondary" size="sm">Log in</x-ui.button>
                </div>
            </div>
        @endguest
    </div>

    <script>
    function communityFeed() {
        return {
            body: '',
            submitting: false,
            error: '',
            livePosts: [],

            init() {},

            async submitPost() {
                if (!this.body.trim() || this.submitting) return;
                this.submitting = true;
                this.error = '';

                try {
                    const res = await fetch('{{ route('community.store') }}', {
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

                    this.livePosts.unshift(data.post);
                    this.body = '';
                } catch {
                    this.error = 'Could not connect. Please try again.';
                } finally {
                    this.submitting = false;
                }
            },

            async deletePost(post, event) {
                if (!confirm('Delete this post?')) return;
                event.target.closest('form').querySelector('button').disabled = true;

                try {
                    const form = event.target.closest('form');
                    const fd = new FormData(form);
                    await fetch(post.delete_url, { method: 'POST', body: fd });
                    this.livePosts = this.livePosts.filter(p => p.id !== post.id);
                } catch {
                    alert('Could not delete post.');
                }
            },
        };
    }
    </script>
</x-layouts.site>
