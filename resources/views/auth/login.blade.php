<x-guest-layout>
    <x-slot:title>Log in</x-slot:title>

    <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Welcome back</h1>
    <p class="mt-2 text-muted">Log in to order fresh produce or manage your farm.</p>

    <x-ui.flash class="mt-6" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" novalidate>
        @csrf

        <x-ui.input name="email" type="email" label="Email address" required autofocus autocomplete="username" inputmode="email" />
        <x-ui.input name="password" type="password" label="Password" required autocomplete="current-password" />

        <div class="flex items-center justify-between gap-4">
            <label for="remember" class="inline-flex min-h-[44px] items-center gap-2 text-sm text-ink">
                <input id="remember" type="checkbox" name="remember" class="h-5 w-5 rounded border-line text-brand-600 focus:ring-brand-500">
                Keep me logged in
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="rounded text-sm font-semibold text-brand-600 hover:text-brand-800">Forgot password?</a>
            @endif
        </div>

        <x-ui.button class="w-full" size="lg">Log in</x-ui.button>
    </form>

    <div class="mt-8 space-y-2 border-t border-line pt-6 text-sm text-muted">
        <p>New here? <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-800">Create a customer account</a></p>
        <p>Selling produce? <a href="{{ route('farmer.register') }}" class="font-semibold text-brand-600 hover:text-brand-800">Join as a farmer</a></p>
    </div>
</x-guest-layout>
