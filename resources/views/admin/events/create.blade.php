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

        {{-- Cloudinary poster — inline Alpine, same pattern as farmer product form --}}
        <div x-data="{
            preview: null,
            posterUrl: '',
            posterPublicId: '',
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
                    const sigRes = await fetch('{{ route('api.cloudinary.sign') }}', { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
                    let sig;
                    try { sig = await sigRes.json(); } catch { throw new Error('Upload service unavailable (' + sigRes.status + ')'); }
                    if (!sigRes.ok || sig.error) throw new Error(sig.error || 'Could not get upload credentials');
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
                        xhr.onload = () => {
                            if (xhr.status === 200) { resolve(JSON.parse(xhr.responseText)); }
                            else { let m = 'Upload failed (' + xhr.status + ')'; try { m = JSON.parse(xhr.responseText).error?.message || m; } catch {} reject(new Error(m)); }
                        };
                        xhr.onerror = () => reject(new Error('Network error'));
                        xhr.open('POST', 'https://api.cloudinary.com/v1_1/' + sig.cloud_name + '/image/upload');
                        xhr.send(fd);
                    });
                    this.posterUrl = result.secure_url;
                    this.posterPublicId = result.public_id;
                    this.preview = result.secure_url;
                    this.progress = 100;
                } catch (err) {
                    this.uploadError = err.message || 'Upload failed. Please try again.';
                    this.preview = null;
                    this.posterUrl = '';
                    this.posterPublicId = '';
                } finally {
                    this.uploading = false;
                }
            }
        }">
            <p class="block text-sm font-semibold text-ink">Event poster (optional)</p>

            <input type="hidden" name="poster_url" :value="posterUrl">
            <input type="hidden" name="poster_public_id" :value="posterPublicId">

            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-start">
                <div class="h-28 w-28 shrink-0 overflow-hidden rounded-xl bg-brand-50 ring-1 ring-line">
                    <template x-if="preview">
                        <img :src="preview" alt="Poster preview" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!preview">
                        <div class="flex h-full w-full items-center justify-center">
                            <svg class="h-8 w-8 text-brand-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 3h18M3 21h18"/></svg>
                        </div>
                    </template>
                </div>
                <div>
                    <input type="file" id="poster" accept="image/*" class="sr-only" :disabled="uploading" @change="handleFile($event)">
                    <label for="poster"
                           :class="uploading ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer hover:bg-brand-50'"
                           class="inline-flex items-center gap-2 rounded-xl bg-white px-4 text-sm font-semibold text-brand-600 ring-1 ring-inset ring-line min-h-[38px] transition-opacity">
                        <span x-show="!uploading">Choose image</span>
                        <span x-show="uploading" x-cloak x-text="'Uploading ' + progress + '%'"></span>
                    </label>
                    <div x-show="uploading" x-cloak class="mt-2 h-1.5 w-40 overflow-hidden rounded-full bg-line">
                        <div class="h-full bg-brand-500 transition-all duration-200" :style="'width:' + progress + '%'"></div>
                    </div>
                    <p class="mt-1.5 text-xs text-muted">Any image format · Max 10 MB</p>
                    <p x-show="uploadError" x-cloak x-text="uploadError" class="mt-1.5 text-sm font-medium text-red-700"></p>
                    <p x-show="posterUrl && !uploading" x-cloak class="mt-1.5 text-xs font-medium text-brand-600">Image uploaded ✓</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <x-ui.button type="submit">Create event</x-ui.button>
            <x-ui.button :href="route('admin.events.index')" variant="secondary">Cancel</x-ui.button>
        </div>
    </form>
</x-layouts.dashboard>
