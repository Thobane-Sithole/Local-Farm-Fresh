<x-guest-layout>
    <x-slot:title>Enter verification code</x-slot:title>

    <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Check your email</h1>
    <p class="mt-2 text-muted">We sent a 6-digit code to <strong class="text-ink">{{ $email }}</strong>. Enter it below together with your new password.</p>

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

    <form method="POST" action="{{ route('password.otp.reset') }}" class="mt-8 space-y-5" novalidate>
        @csrf

        <x-ui.input
            name="code"
            type="text"
            label="6-digit code"
            inputmode="numeric"
            autocomplete="one-time-code"
            maxlength="6"
            placeholder="000000"
            required
        />

        <x-ui.input
            name="password"
            type="password"
            label="New password"
            :toggleable="true"
            required
            autocomplete="new-password"
        />

        <x-ui.input
            name="password_confirmation"
            type="password"
            label="Confirm new password"
            :toggleable="true"
            required
            autocomplete="new-password"
        />

        <x-ui.button class="w-full" size="lg">Reset password</x-ui.button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        Didn't receive a code?
        <a href="{{ route('password.request') }}" class="font-semibold text-brand-600 hover:text-brand-800">Try again</a>
    </p>
</x-guest-layout>
