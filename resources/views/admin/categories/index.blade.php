<x-layouts.dashboard title="Categories" area="admin">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Categories</h1>
            <p class="mt-1 text-muted">Products are organised into categories visible to customers.</p>
        </div>
        <x-ui.button :href="route('admin.categories.create')" class="w-full sm:w-auto">New category</x-ui.button>
    </div>

    @if ($categories->isEmpty())
        <x-ui.empty-state title="No categories yet" class="mt-8"
                          action="Create first category" :action-href="route('admin.categories.create')">
            Categories help customers browse produce by type.
        </x-ui.empty-state>
    @else
        <div class="mt-6 overflow-hidden rounded-card bg-white shadow-card ring-1 ring-line/60">
            <table class="min-w-full divide-y divide-line">
                <thead>
                    <tr class="bg-brand-50">
                        <th scope="col" class="py-3.5 pl-5 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-muted">Name</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted sm:table-cell">Products</th>
                        <th scope="col" class="hidden px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted md:table-cell">Order</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-muted">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-5"><span class="sr-only">Edit</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($categories as $category)
                        <tr>
                            <td class="py-3.5 pl-5 pr-3">
                                <p class="text-sm font-semibold text-ink">{{ $category->name }}</p>
                                @if ($category->description)
                                    <p class="text-xs text-muted">{{ Str::limit($category->description, 60) }}</p>
                                @endif
                            </td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted sm:table-cell">{{ $category->products_count }}</td>
                            <td class="hidden px-3 py-3.5 text-sm text-muted md:table-cell">{{ $category->sort_order }}</td>
                            <td class="px-3 py-3.5">
                                @if ($category->is_active)
                                    <span class="inline-flex items-center rounded-full bg-brand-100 px-2 py-0.5 text-xs font-semibold text-brand-800">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-line px-2 py-0.5 text-xs font-semibold text-muted">Hidden</span>
                                @endif
                            </td>
                            <td class="py-3.5 pl-3 pr-5 text-right text-sm">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="font-semibold text-brand-600 hover:text-brand-800">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.dashboard>
