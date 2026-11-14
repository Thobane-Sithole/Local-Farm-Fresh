<x-layouts.dashboard title="New event" area="admin">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Events
        </a>
    </div>

    <h1 class="text-2xl font-extrabold tracking-tight">New event</h1>

    <form method="POST" action="{{ route('admin.events.store') }}" class="mt-6 max-w-xl space-y-5">
        @csrf

        <div>
            <label for="title" class="block text-sm font-semibold text-ink">Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="150" required
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-3 py-2.5 text-sm text-ink focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('title') border-red-400 @enderror">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-ink">Description</label>
            <textarea id="description" name="description" rows="4" maxlength="2000"
                      class="mt-1.5 block w-full resize-none rounded-xl border border-line bg-white px-3 py-2.5 text-sm text-ink focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="location" class="block text-sm font-semibold text-ink">Location <span class="text-red-500">*</span></label>
            <input type="text" id="location" name="location" value="{{ old('location') }}" maxlength="200" required
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-3 py-2.5 text-sm text-ink focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('location') border-red-400 @enderror"
                   placeholder="e.g. Rosebank Market, Johannesburg">
            @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="starts_at" class="block text-sm font-semibold text-ink">Date & time <span class="text-red-500">*</span></label>
            <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" required
                   class="mt-1.5 block w-full rounded-xl border border-line bg-white px-3 py-2.5 text-sm text-ink focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('starts_at') border-red-400 @enderror">
            @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Cloudinary poster upload --}}
        <div x-data="cloudinaryUpload()" class="space-y-2">
            <p class="block text-sm font-semibold text-ink">Event poster (optional)</p>

            <div class="flex items-center gap-3">
                <label class="cursor-pointer">
                    <input type="file" accept="image/*" class="sr-only" @change="upload($event)">
                    <span class="inline-flex items-center gap-2 rounded-xl border border-line bg-white px-4 py-2.5 text-sm font-semibold text-ink hover:bg-brand-50 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                        Upload image
                    </span>
                </label>
                <span x-text="status" class="text-xs text-muted"></span>
            </div>

            <template x-if="previewUrl">
                <img :src="previewUrl" alt="Poster preview" class="mt-2 w-full max-w-xs rounded-xl object-cover ring-1 ring-line/60">
            </template>

            <input type="hidden" name="poster_url" :value="posterUrl">
            <input type="hidden" name="poster_public_id" :value="posterPublicId">
        </div>

        <div class="flex gap-3 pt-2">
            <x-ui.button type="submit">Create event</x-ui.button>
            <x-ui.button :href="route('admin.events.index')" variant="secondary">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.dashboard>

@push('scripts')
<script>
function cloudinaryUpload() {
    return {
        status: '',
        previewUrl: null,
        posterUrl: '',
        posterPublicId: '',
        async upload(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.status = 'Uploading…';
            try {
                const sigRes = await fetch('{{ route('api.cloudinary.sign') }}');
                const { signature, timestamp, cloud_name, api_key, folder } = await sigRes.json();
                const fd = new FormData();
                fd.append('file', file);
                fd.append('api_key', api_key);
                fd.append('timestamp', timestamp);
                fd.append('signature', signature);
                if (folder) fd.append('folder', folder);
                const res = await fetch(`https://api.cloudinary.com/v1_1/${cloud_name}/image/upload`, { method: 'POST', body: fd });
                const data = await res.json();
                this.posterUrl = data.secure_url;
                this.posterPublicId = data.public_id;
                this.previewUrl = data.secure_url;
                this.status = 'Uploaded ✓';
            } catch {
                this.status = 'Upload failed. Try again.';
            }
        }
    };
}
</script>
@endpush
