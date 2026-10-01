@props(['label', 'value', 'href' => null, 'highlight' => false])

@php
    $tag = $href ? 'a' : 'div';
    $classes = 'block rounded-card p-4 sm:p-5 '.($highlight
        ? 'bg-brand-800 text-white'
        : 'bg-white text-ink shadow-card ring-1 ring-line/60');
    if ($href) {
        $classes .= $highlight ? ' hover:bg-brand-900' : ' hover:ring-brand-200';
    }
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => $classes]) }}>
    <p class="text-sm font-medium {{ $highlight ? 'text-brand-100' : 'text-muted' }}">{{ $label }}</p>
    <p class="mt-1 text-3xl font-extrabold tabular-nums tracking-tight">{{ number_format($value) }}</p>
</{{ $tag }}>
