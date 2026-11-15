@auth
@php
    $idleMinutes = (int) config('session.idle_timeout', config('session.lifetime', 30));
    $warnMinutes = max(1, $idleMinutes - 2);
@endphp
<div
    x-data="idleTimeout({{ $idleMinutes }}, {{ $warnMinutes }})"
    x-init="start()"
    @mousemove.window="reset()"
    @keydown.window="reset()"
    @click.window="reset()"
    @touchstart.window="reset()"
    @scroll.window="reset()"
>
    {{-- Warning modal --}}
    <div
        x-show="showWarning"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-labelledby="idle-title"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
    >
        <div class="w-full max-w-sm rounded-2xl bg-white shadow-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <h2 id="idle-title" class="text-base font-bold text-ink">Still there?</h2>
            </div>

            <p class="text-sm text-muted mb-1">
                You'll be logged out in
            </p>
            <p class="text-3xl font-extrabold text-brand-600 mb-5" x-text="countdown"></p>
            <p class="text-xs text-muted mb-6">
                For your security, inactive sessions are automatically closed.
            </p>

            <div class="flex gap-3">
                <button
                    type="button"
                    @click="keepAlive()"
                    class="flex-1 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition-colors"
                >
                    Stay logged in
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink hover:bg-zinc-50 transition-colors"
                    >
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function idleTimeout(idleMinutes, warnMinutes) {
    return {
        showWarning: false,
        countdown: '',
        _idleTimer: null,
        _warnTimer: null,
        _countdownInterval: null,
        _logoutAt: null,

        start() {
            this._scheduleWarn();
        },

        reset() {
            if (this.showWarning) return; // user clicked "stay" via keepAlive
            clearTimeout(this._idleTimer);
            clearTimeout(this._warnTimer);
            this._scheduleWarn();
        },

        _scheduleWarn() {
            const warnMs  = warnMinutes  * 60 * 1000;
            const idleMs  = idleMinutes  * 60 * 1000;
            clearTimeout(this._warnTimer);
            clearTimeout(this._idleTimer);
            clearInterval(this._countdownInterval);

            this._warnTimer = setTimeout(() => this._showWarning(idleMs - warnMs), warnMs);
        },

        _showWarning(remainingMs) {
            this.showWarning  = true;
            this._logoutAt    = Date.now() + remainingMs;
            this._tick();
            this._countdownInterval = setInterval(() => this._tick(), 500);
            this._idleTimer = setTimeout(() => this._forceLogout(), remainingMs);
        },

        _tick() {
            const ms  = Math.max(0, this._logoutAt - Date.now());
            const m   = Math.floor(ms / 60000);
            const s   = Math.floor((ms % 60000) / 1000);
            this.countdown = m > 0
                ? `${m}:${String(s).padStart(2, '0')}`
                : `${s}s`;
        },

        async keepAlive() {
            clearTimeout(this._idleTimer);
            clearInterval(this._countdownInterval);
            this.showWarning = false;
            try {
                await fetch('{{ route("keep-alive") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'Accept': 'application/json',
                    },
                });
            } catch (_) {}
            this._scheduleWarn();
        },

        _forceLogout() {
            clearInterval(this._countdownInterval);
            document.querySelector('#idle-logout-form')?.submit();
        },
    };
}
</script>

{{-- Hidden logout form used by _forceLogout --}}
<form id="idle-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>
@endauth
