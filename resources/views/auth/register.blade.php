<x-guest-layout>
    <x-slot:title>Create an account</x-slot:title>

    <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Create your account</h1>
    <p class="mt-2 text-muted">Order from local farms and pay cash when your order arrives.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5" novalidate>
        @csrf

        <x-ui.input name="name" label="Full name" required autofocus autocomplete="name" />
        <x-ui.input name="email" type="email" label="Email address" required autocomplete="email" inputmode="email" />
        <x-ui.input name="phone" type="tel" label="Phone number" autocomplete="tel" inputmode="tel"
                    placeholder="072 123 4567" hint="Farmers call this number if they need directions for delivery." />
        <x-ui.input name="password" type="password" label="Password" required autocomplete="new-password" hint="At least 8 characters." />
        <x-ui.input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />

        <x-ui.button class="w-full" size="lg">Create account</x-ui.button>
    </form>

    <div class="mt-8 space-y-2 border-t border-line pt-6 text-sm text-muted">
        <p>Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-800">Log in</a></p>
        <p>Selling produce? <a href="{{ route('farmer.register') }}" class="font-semibold text-brand-600 hover:text-brand-800">Join as a farmer</a></p>
    </div>
</x-guest-layout>
