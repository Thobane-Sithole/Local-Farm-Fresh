<x-layouts.dashboard title="Farm profile" area="farmer">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-brand-600">{{ $farmer->farm_name }}</p>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">Farm profile</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('farmer.profile.update') }}" enctype="multipart/form-data" class="mt-8 space-y-8">
        @csrf
        @method('PUT')

        {{-- Profile image --}}
        <section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
            <h2 class="text-base font-bold text-ink">Farm photo</h2>
            <p class="mt-0.5 text-sm text-muted">A photo of your farm helps customers recognise your produce.</p>

            <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center"
                 x-data="{ preview: '{{ $farmer->profile_image_url }}' }">
                <div class="h-24 w-24 shrink-0 overflow-hidden rounded-full bg-brand-100 ring-2 ring-line">
                    <template x-if="preview">
                        <img :src="preview" alt="Farm photo preview" class="h-full w-full object-cover">
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
                        id="profile_image"
                        name="profile_image"
                        accept="image/jpeg,image/png,image/webp"
                        class="sr-only"
                        @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                    >
                    <label for="profile_image" class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 text-sm font-semibold text-brand-600 ring-1 ring-inset ring-line min-h-[38px] hover:bg-brand-50">
                        Choose photo
                    </label>
                    <p class="mt-1.5 text-xs text-muted">JPEG, PNG or WebP · Max 5 MB</p>
                    @error('profile_image')
                        <p class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- Farm details --}}
        <section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
            <h2 class="text-base font-bold text-ink">Farm details</h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <x-ui.input name="farm_name" label="Farm name" :value="$farmer->farm_name" required class="sm:col-span-2" />
                <x-ui.textarea name="description" label="About your farm" :value="$farmer->description" :rows="4" hint="Tell customers what you grow and how you farm." class="sm:col-span-2" />
            </div>
        </section>

        {{-- Location --}}
        <section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
            <h2 class="text-base font-bold text-ink">Location</h2>

            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <x-ui.select
                    name="province"
                    label="Province"
                    :options="$provinces"
                    :value="$farmer->province?->value"
                    required
                />
                <x-ui.input name="municipality" label="Municipality / local area" :value="$farmer->municipality" required />
                <x-ui.input name="town" label="Town" :value="$farmer->town" />
                <x-ui.input name="farm_address" label="Farm address" :value="$farmer->farm_address" hint="Optional street address for delivery instructions." class="sm:col-span-2" />
            </div>
        </section>

        {{-- Delivery --}}
        <section class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
            <h2 class="text-base font-bold text-ink">Delivery</h2>

            <div class="mt-5 max-w-xs">
                <x-ui.input
                    name="delivery_fee"
                    label="Delivery fee (R)"
                    type="number"
                    step="0.01"
                    min="0"
                    max="9999.99"
                    :value="$farmer->delivery_fee"
                    hint="Set to 0 for free delivery. Customers pay cash on delivery."
                    required
                />
            </div>
        </section>

        <div class="flex items-center justify-end gap-3">
            <x-ui.button variant="secondary" href="{{ route('farmer.dashboard') }}" type="button">Cancel</x-ui.button>
            <x-ui.button type="submit">Save profile</x-ui.button>
        </div>
    </form>
</x-layouts.dashboard>
