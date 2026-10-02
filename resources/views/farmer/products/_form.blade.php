{{-- Shared form fields for create and edit. $product is null on create. --}}

{{-- Product image — browser-direct Cloudinary upload (avoids server timeout) --}}
<section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
    <h2 class="text-base font-bold text-ink">Product photo</h2>
    <p class="mt-0.5 text-sm text-muted">A clear photo of your produce increases sales.</p>

    @php
        $existingUrl      = $product?->primaryImage?->url;
        $existingPublicId = $product?->primaryImage?->public_id;
    @endphp

    <div class="mt-4" x-data="{
        preview: {{ $existingUrl ? json_encode($existingUrl) : 'null' }},
        imageUrl: {{ $existingUrl ? json_encode($existingUrl) : "''" }},
        imagePublicId: {{ $existingPublicId ? json_encode($existingPublicId) : "''" }},
        uploading: false,
        progress: 0,
        uploadError: null,
        async handleFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.uploadError = null;
            this.uploading = true;
            this.progress = 0;
            this.preview = URL.createObjectURL(file);
            try {
                const sig = await fetch('{{ route('api.cloudinary.sign') }}').then(r => r.json());
                const fd = new FormData();
                fd.append('file', file);
                fd.append('api_key', sig.api_key);
                fd.append('timestamp', sig.timestamp);
                fd.append('signature', sig.signature);
                fd.append('folder', sig.folder);
                const result = await new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', e => {
                        if (e.lengthComputable) this.progress = Math.round((e.loaded / e.total) * 100);
                    });
                    xhr.onload = () => xhr.status === 200
                        ? resolve(JSON.parse(xhr.responseText))
                        : reject(new Error('Upload failed (' + xhr.status + ')'));
                    xhr.onerror = () => reject(new Error('Network error'));
                    xhr.open('POST', `https://api.cloudinary.com/v1_1/${sig.cloud_name}/image/upload`);
                    xhr.send(fd);
                });
                this.imageUrl = result.secure_url;
                this.imagePublicId = result.public_id;
                this.preview = result.secure_url;
            } catch (err) {
                this.uploadError = 'Upload failed. Please try again.';
                this.preview = {{ $existingUrl ? json_encode($existingUrl) : 'null' }};
                this.imageUrl = {{ $existingUrl ? json_encode($existingUrl) : "''" }};
            } finally {
                this.uploading = false;
                this.progress = 0;
            }
        }
    }">
        <input type="hidden" name="image_url" :value="imageUrl">
        <input type="hidden" name="image_public_id" :value="imagePublicId">

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
            <div class="flex-1">
                <input
                    type="file"
                    id="image"
                    accept="image/jpeg,image/png,image/webp"
                    class="sr-only"
                    :disabled="uploading"
                    @change="handleFile($event)"
                >
                <label for="image"
                       :class="uploading ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer hover:bg-brand-50'"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4 text-sm font-semibold text-brand-600 ring-1 ring-inset ring-line min-h-[38px] transition-opacity">
                    <span x-show="!uploading">{{ $existingUrl ? 'Replace photo' : 'Choose photo' }}</span>
                    <span x-show="uploading" x-cloak x-text="'Uploading ' + progress + '%'"></span>
                </label>

                {{-- Upload progress bar --}}
                <div x-show="uploading" x-cloak class="mt-2 h-1.5 w-48 overflow-hidden rounded-full bg-line">
                    <div class="h-full bg-brand-500 transition-all duration-200" :style="`width:${progress}%`"></div>
                </div>

                <p class="mt-1.5 text-xs text-muted">JPEG, PNG or WebP · Max 10 MB</p>
                <p x-show="uploadError" x-cloak x-text="uploadError" class="mt-1.5 text-sm font-medium text-red-700"></p>
                @error('image_url')
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
