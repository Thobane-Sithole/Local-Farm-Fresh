<x-guest-layout>
    <x-slot:title>Reset password</x-slot:title>

    <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Forgot your password?</h1>
    <p class="mt-2 text-muted">Enter your email address and we'll send a 6-digit verification code to reset your password.</p>

    <x-ui.flash class="mt-6" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5" novalidate>
        @csrf

        <x-ui.input name="email" type="email" label="Email address" required autofocus autocomplete="username" inputmode="email" />

        <x-ui.button class="w-full" size="lg">Send reset code</x-ui.button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-800">Back to log in</a>
    </p>
</x-guest-layout>
