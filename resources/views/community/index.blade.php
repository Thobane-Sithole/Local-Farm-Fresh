<x-layouts.site title="Community Hub">
    <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink">Community Hub</h1>
            <p class="mt-1 text-muted">Share recipes, tips and stories with the Local Farm Fresh community.</p>
        </div>

        @auth
            @if (auth()->user()->hasUsername())
                <div class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 mb-8">
                    <form method="POST" action="{{ route('community.store') }}">
                        @csrf
                        <label for="body" class="sr-only">What's on your mind?</label>
                        <textarea
                            id="body"
                            name="body"
                            rows="3"
                            placeholder="Share a recipe, tip, or story…"
                            maxlength="2000"
                            class="block w-full resize-none rounded-xl border border-line bg-white p-3 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('body') border-red-400 @enderror"
                        >{{ old('body') }}</textarea>
                        @error('body')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs text-muted">Posting as <strong class="text-ink">@{{ auth()->user()->username }}</strong></span>
                            <x-ui.button type="submit" size="sm">Post</x-ui.button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth

        @if ($posts->isEmpty())
            <div class="rounded-card bg-white p-12 text-center shadow-card ring-1 ring-line/60">
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
                                    <span class="text-sm font-bold text-ink">@{{ $post->user->username ?? $post->user->name }}</span>
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
</x-layouts.site>
