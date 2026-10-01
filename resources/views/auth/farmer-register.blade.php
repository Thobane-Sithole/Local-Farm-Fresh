<x-guest-layout>
    <x-slot:title>Sell your produce</x-slot:title>

    <x-slot:aside>
        <p class="max-w-md text-4xl font-extrabold leading-[1.1] tracking-tight">
            Sell to your community, straight from your farm.
        </p>
        <ol class="mt-8 max-w-sm space-y-4 text-brand-100">
            @foreach (['Create your farm profile', 'Add your produce with a photo and price', 'Get notified when someone orders', 'Deliver and collect cash'] as $i => $step)
                <li class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-300 text-sm font-bold text-brand-900">{{ $i + 1 }}</span>
                    <span class="pt-0.5">{{ $step }}</span>
                </li>
            @endforeach
        </ol>
    </x-slot:aside>

    <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Join as a farmer</h1>
    <p class="mt-2 text-muted">It's free. You can start adding products as soon as you finish.</p>

    <form method="POST" action="{{ route('farmer.register.store') }}" class="mt-8 space-y-8" novalidate>
        @csrf

        @if ($errors->any())
            <x-ui.alert type="error">Some details need fixing. Check the highlighted fields below.</x-ui.alert>
        @endif

        <fieldset class="space-y-5">
            <legend class="text-lg font-bold text-ink">About you</legend>
            <x-ui.input name="name" label="Full name" required autofocus autocomplete="name" />
            <x-ui.input name="phone" type="tel" label="Phone number" required autocomplete="tel" inputmode="tel"
                        placeholder="072 123 4567" hint="Customers and our team will use this to reach you about orders." />
            <x-ui.input name="email" type="email" label="Email address" required autocomplete="email" inputmode="email"
                        hint="New orders are also sent here." />
            <x-ui.input name="password" type="password" label="Password" required autocomplete="new-password" hint="At least 8 characters." />
            <x-ui.input name="password_confirmation" type="password" label="Confirm password" required autocomplete="new-password" />
        </fieldset>

        <fieldset class="space-y-5 border-t border-line pt-8">
            <legend class="text-lg font-bold text-ink">Your farm</legend>
            <x-ui.input name="farm_name" label="Farm or business name" required placeholder="e.g. Mokoena Family Farm" />
            <x-ui.select name="province" label="Province" :options="$provinces" placeholder="Choose your province" required />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="municipality" label="Municipality" required placeholder="e.g. Polokwane" />
                <x-ui.input name="town" label="Town or village" placeholder="e.g. Seshego" />
            </div>
            <x-ui.input name="farm_address" label="Farm address" autocomplete="street-address"
                        hint="Only shown to customers after they order from you." />
            <x-ui.textarea name="description" label="Tell customers about your farm" rows="3"
                           placeholder="What do you grow, and how?" />
            <p class="rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-900">You can add a photo of yourself or your farm after signing up.</p>
        </fieldset>

        <div>
            <x-ui.button class="w-full" size="lg">Create farmer account</x-ui.button>
            <p class="mt-4 text-center text-sm text-muted">
                Already selling with us? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-800">Log in</a>
            </p>
        </div>
    </form>
</x-guest-layout>
