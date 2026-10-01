{{-- Shared form fields for create and edit. $product is null on create. --}}

{{-- Product image --}}
<section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
    <h2 class="text-base font-bold text-ink">Product photo</h2>
    <p class="mt-0.5 text-sm text-muted">A clear photo of your produce increases sales.</p>

    @php $existingUrl = $product?->primaryImage?->url; @endphp

    <div class="mt-4" x-data="{ preview: '{{ $existingUrl }}' }">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
            <div class="h-32 w-32 shrink-0 overflow-hidden rounded-xl bg-brand-100 ring-1 ring-line">
                <template x-if="preview">
                    <img :src="preview" alt="Product photo preview" class="h-full w-full object-cover">
                </template>
                <template x-if="!preview">
                    <div class="flex h-full w-full items-center justify-center">
                        <svg class="h-10 w-10 text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3 3h18M3 21h18"/>
                        </svg>
                    </div>
                </template>
            </div>
            <div>
                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="sr-only"
                    @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                >
                <label for="image" class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 text-sm font-semibold text-brand-600 ring-1 ring-inset ring-line min-h-[38px] hover:bg-brand-50">
                    {{ $existingUrl ? 'Replace photo' : 'Choose photo' }}
                </label>
                <p class="mt-1.5 text-xs text-muted">JPEG, PNG or WebP · Max 5 MB</p>
                @error('image')
                    <p class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</section>

{{-- Details --}}
<section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
    <h2 class="text-base font-bold text-ink">Product details</h2>

    <div class="mt-5 grid gap-5 sm:grid-cols-2">
        <x-ui.input name="name" label="Product name" :value="$product?->name" required class="sm:col-span-2" />

        <x-ui.select
            name="category_id"
            label="Category"
            :options="$categories->toArray()"
            :value="$product?->category_id"
            required
        />

        <x-ui.select
            name="unit"
            label="Unit"
            :options="collect($units)->mapWithKeys(fn ($u) => [$u->value => $u->label()])->toArray()"
            :value="$product?->unit?->value"
            required
        />

        <x-ui.input
            name="price"
            label="Price (R)"
            type="number"
            step="0.01"
            min="0.01"
            max="99999.99"
            :value="$product?->price"
            required
        />

        <x-ui.input
            name="quantity_available"
            label="Quantity available"
            type="number"
            min="0"
            max="99999"
            :value="$product?->quantity_available ?? 0"
            required
        />

        <x-ui.input
            name="short_description"
            label="Short description"
            :value="$product?->short_description"
            hint="Shown on listing cards (max 160 characters)."
            maxlength="160"
            class="sm:col-span-2"
        />

        <x-ui.textarea
            name="description"
            label="Full description"
            :value="$product?->description"
            :rows="5"
            hint="Harvest time, storage tips, how it's grown — anything that helps the customer."
            class="sm:col-span-2"
        />
    </div>
</section>

{{-- Availability --}}
<section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
    <h2 class="text-base font-bold text-ink">Availability</h2>

    <label class="mt-4 flex cursor-pointer items-center gap-3">
        <input
            type="hidden"
            name="is_available"
            value="0"
        >
        <input
            type="checkbox"
            name="is_available"
            value="1"
            class="h-5 w-5 rounded border-line text-brand-600 focus:ring-brand-500"
            {{ old('is_available', $product?->is_available ?? true) ? 'checked' : '' }}
        >
        <span class="text-sm font-medium text-ink">List this product for sale</span>
    </label>
    <p class="ml-8 mt-0.5 text-sm text-muted">Uncheck to temporarily hide this product without deleting it.</p>
</section>
