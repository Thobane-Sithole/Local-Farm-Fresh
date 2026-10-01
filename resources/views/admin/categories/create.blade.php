<x-layouts.dashboard title="New category" area="admin">
    <div>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Categories
        </a>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">New category</h1>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-8">
        @csrf

        <div class="rounded-card bg-white p-5 shadow-card ring-1 ring-line/60 sm:p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-ui.input name="name" label="Name" required class="sm:col-span-2" />
                <x-ui.input name="description" label="Description" />
                <x-ui.input name="sort_order" label="Sort order" type="number" min="0" max="9999" value="0"
                            hint="Lower numbers appear first." />
                <label class="flex cursor-pointer items-center gap-3 pt-6">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked
                           class="h-5 w-5 rounded border-line text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-ink">Active (visible to customers)</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-3">
            <x-ui.button variant="secondary" href="{{ route('admin.categories.index') }}" type="button">Cancel</x-ui.button>
            <x-ui.button type="submit">Create category</x-ui.button>
        </div>
    </form>
</x-layouts.dashboard>
