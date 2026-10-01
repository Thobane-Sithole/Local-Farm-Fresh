<x-layouts.dashboard title="Edit user" area="admin">
    <div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Users
        </a>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $user->name }}</h1>
        <p class="mt-1 text-sm text-muted">{{ $user->email }} &middot; joined {{ $user->created_at->format('d M Y') }}</p>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        {{-- Left: role form --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
                    <h2 class="text-base font-bold text-ink">Account role</h2>
                    <p class="mt-1 text-sm text-muted">Changing role takes effect on their next page load.</p>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        @foreach (\App\Enums\UserRole::cases() as $role)
                            <label @class([
                                'flex cursor-pointer items-center gap-3 rounded-xl border-2 p-4 transition-colors',
                                'border-brand-500 bg-brand-50' => $user->role === $role,
                                'border-line hover:border-brand-200' => $user->role !== $role,
                                'cursor-not-allowed opacity-50' => $user->id === auth()->id(),
                            ])>
                                <input type="radio" name="role" value="{{ $role->value }}"
                                       class="sr-only" {{ $user->role === $role ? 'checked' : '' }}
                                       {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <div>
                                    <p class="text-sm font-semibold text-ink">{{ $role->label() }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @if ($user->id === auth()->id())
                        <p class="mt-3 text-xs text-muted">You cannot change your own role.</p>
                    @endif
                </div>

                @if ($user->farmerProfile)
                    <div class="mt-4 rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
                        <h2 class="text-base font-bold text-ink">Farm profile</h2>
                        <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-muted">Farm name</dt>
                                <dd class="font-medium text-ink">{{ $user->farmerProfile->farm_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted">Location</dt>
                                <dd class="font-medium text-ink">{{ $user->farmerProfile->locationLabel() }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted">Delivery fee</dt>
                                <dd class="font-medium text-ink">R{{ number_format((float) $user->farmerProfile->delivery_fee, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                @endif

                <div class="mt-6 flex justify-end gap-3">
                    <x-ui.button variant="secondary" href="{{ route('admin.users.index') }}">Cancel</x-ui.button>
                    <x-ui.button type="submit" :disabled="$user->id === auth()->id()">Save changes</x-ui.button>
                </div>
            </form>
        </div>

        {{-- Right: status panel --}}
        <div class="space-y-4">
            <div class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60">
                <h2 class="text-base font-bold text-ink">Account status</h2>
                <div class="mt-3">
                    @if ($user->isSuspended())
                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">Suspended</span>
                        @if ($user->suspension_reason)
                            <p class="mt-2 text-xs text-muted">Reason: {{ $user->suspension_reason }}</p>
                        @endif
                        @if ($user->suspended_at)
                            <p class="mt-1 text-xs text-muted">Since {{ $user->suspended_at->format('d M Y') }}</p>
                        @endif
                    @else
                        <span class="inline-flex items-center rounded-full bg-brand-100 px-3 py-1 text-sm font-semibold text-brand-800">Active</span>
                    @endif
                </div>

                @if ($user->id !== auth()->id())
                    <div class="mt-4 border-t border-line pt-4">
                        @if ($user->isSuspended())
                            <form method="POST" action="{{ route('admin.users.reinstate', $user) }}">
                                @csrf
                                <x-ui.button type="submit" class="w-full">Reinstate account</x-ui.button>
                            </form>
                        @else
                            <form x-data="{ open: false }" method="POST" action="{{ route('admin.users.suspend', $user) }}">
                                @csrf
                                <button type="button" @click="open = !open"
                                        class="w-full rounded-xl border-2 border-red-200 bg-red-50 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                                    Suspend account
                                </button>
                                <div x-show="open" class="mt-3 space-y-3">
                                    <textarea name="reason" rows="2"
                                              placeholder="Reason (optional — shown to you, not the user)"
                                              class="block w-full rounded-xl border border-line px-3 py-2 text-sm text-ink placeholder-muted focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"></textarea>
                                    <x-ui.button type="submit" variant="danger" class="w-full">Confirm suspension</x-ui.button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.dashboard>
