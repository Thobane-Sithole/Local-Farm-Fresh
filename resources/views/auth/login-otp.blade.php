<x-guest-layout>
    <x-slot:title>Verify your identity</x-slot:title>

    <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Two-step verification</h1>
    <p class="mt-2 text-muted">We sent a 6-digit code to your email address. Enter it below to complete your login.</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if (session('dev_otp'))
        <div class="mt-4 rounded-lg bg-amber-50 border border-amber-300 px-4 py-3 text-sm text-amber-800">
            <strong>Dev mode</strong> — your code is: <span class="font-mono font-bold tracking-widest text-lg">{{ session('dev_otp') }}</span>
        </div>
    @endif

    <form method="POST" action="/login/otp" class="mt-8 space-y-5" novalidate>
        @csrf

        <x-ui.input
            name="code"
            type="text"
            label="Verification code"
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="6"
            placeholder="000000"
            autofocus
            required
        />

        <x-ui.button class="w-full" size="lg">Verify &amp; log in</x-ui.button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-3 text-sm text-muted">
        <form method="POST" action="{{ route('login.otp.resend') }}">
            @csrf
            <button type="submit" class="font-semibold text-brand-600 hover:text-brand-800">
                Resend code
            </button>
        </form>

        <a href="{{ route('login') }}" class="hover:text-ink">Use a different account</a>
    </div>
</x-guest-layout>
