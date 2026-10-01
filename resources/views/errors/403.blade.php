<x-layouts.site title="No access">
    <div class="mx-auto max-w-lg px-4 py-20 text-center">
        <h1 class="text-3xl font-extrabold tracking-tight">You don't have access to this page</h1>
        <p class="mt-3 text-muted">{{ $exception->getMessage() ?: 'This page belongs to a different type of account.' }}</p>
        <x-ui.button :href="auth()->check() ? route('dashboard') : route('home')" class="mt-8">Go to my home page</x-ui.button>
    </div>
</x-layouts.site>
