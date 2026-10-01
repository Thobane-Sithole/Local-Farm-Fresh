<x-layouts.dashboard title="Edit product" area="farmer">
    <div>
        <a href="{{ route('farmer.products.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Products
        </a>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $product->name }}</h1>
    </div>

    <form id="product-edit-form" method="POST" action="{{ route('farmer.products.update', $product) }}" enctype="multipart/form-data" class="mt-8 space-y-8">
        @csrf
        @method('PUT')

        @include('farmer.products._form', ['product' => $product])

        <div class="flex items-center justify-between">
            <x-ui.button variant="danger" type="button" size="sm"
                         x-data
                         @click="if (confirm('Delete this product? This cannot be undone.')) document.getElementById('delete-product-form').submit()">
                Delete product
            </x-ui.button>

            <div class="flex items-center gap-3">
                <x-ui.button variant="secondary" href="{{ route('farmer.products.index') }}" type="button">Cancel</x-ui.button>
                <x-ui.button type="submit">Save changes</x-ui.button>
            </div>
        </div>
    </form>

    {{-- Delete form lives outside the edit form to avoid nesting --}}
    <form id="delete-product-form" method="POST" action="{{ route('farmer.products.destroy', $product) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-layouts.dashboard>
