@props(['type' => 'success'])

@php
    $styles = [
        'success' => 'bg-brand-100 text-brand-900 ring-brand-200',
        'error' => 'bg-red-50 text-red-900 ring-red-200',
        'info' => 'bg-white text-ink ring-line',
    ][$type] ?? 'bg-white text-ink ring-line';
@endphp

<div
    role="{{ $type === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-xl px-4 py-3 text-sm font-medium ring-1 ring-inset {$styles}"]) }}
>
    @if ($type === 'success')
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.6l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/></svg>
    @elseif ($type === 'error')
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-11.5a.75.75 0 0 1 1.5 0v4a.75.75 0 0 1-1.5 0v-4ZM10 14.75a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
    @endif
    <div>{{ $slot }}</div>
</div>
