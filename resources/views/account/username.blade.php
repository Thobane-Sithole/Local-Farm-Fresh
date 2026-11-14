<x-layouts.site title="Choose your username">
    <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <div class="rounded-card bg-white p-8 shadow-card ring-1 ring-line/60">
            <h1 class="text-2xl font-extrabold tracking-tight text-ink">Pick a username</h1>
            <p class="mt-2 text-sm text-muted">Your username is how the community will know you. You can change it later.</p>

            <form method="POST" action="{{ route('username.update') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-semibold text-ink">Username</label>
                    <div class="relative mt-1.5">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-muted">@</span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username', auth()->user()->username) }}"
                            placeholder="your_username"
                            maxlength="30"
                            class="block w-full rounded-xl border border-line bg-white py-2.5 pl-7 pr-3 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('username') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            required
                        >
                    </div>
                    <p class="mt-1.5 text-xs text-muted">3–30 characters. Letters, numbers, dashes and underscores only.</p>
                    @error('username')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-ui.button type="submit" class="w-full">Save username</x-ui.button>
            </form>
        </div>
    </div>
</x-layouts.site>
