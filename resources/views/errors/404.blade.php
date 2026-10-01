<x-layouts.site title="Page not found">
    <div class="mx-auto max-w-lg px-4 py-20 text-center">
        <h1 class="text-3xl font-extrabold tracking-tight">We couldn't find that page</h1>
        <p class="mt-3 text-muted">It may have been moved, or the product is no longer listed.</p>
        <x-ui.button :href="route('home')" class="mt-8">Back to the homepage</x-ui.button>
    </div>
</x-layouts.site>
