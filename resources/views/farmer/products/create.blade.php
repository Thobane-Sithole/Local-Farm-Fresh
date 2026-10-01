<x-layouts.dashboard title="Add product" area="farmer">
    <div>
        <a href="{{ route('farmer.products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Products
        </a>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Add a product</h1>
    </div>

    <form method="POST" action="{{ route('farmer.products.store') }}" enctype="multipart/form-data" class="mt-8 space-y-8">
        @csrf

        @include('farmer.products._form', ['product' => null])

        <div class="flex items-center justify-end gap-3">
            <x-ui.button variant="secondary" href="{{ route('farmer.products.index') }}" type="button">Cancel</x-ui.button>
            <x-ui.button type="submit">Save product</x-ui.button>
        </div>
    </form>
</x-layouts.dashboard>
