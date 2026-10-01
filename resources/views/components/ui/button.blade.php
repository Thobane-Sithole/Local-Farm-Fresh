@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'submit',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition-colors duration-150 disabled:cursor-not-allowed disabled:opacity-60';

    $sizes = [
        'sm' => 'min-h-[38px] px-3.5 text-sm',
        'md' => 'min-h-[46px] px-5 text-[0.95rem]',   // 44px+ touch target
        'lg' => 'min-h-[52px] px-6 text-base',
    ];

    $variants = [
        'primary' => 'bg-brand-600 text-white hover:bg-brand-800 active:bg-brand-900',
        'secondary' => 'bg-white text-brand-600 ring-1 ring-inset ring-line hover:bg-brand-50 hover:ring-brand-200',
        'ghost' => 'text-brand-600 hover:bg-brand-50',
        'danger' => 'bg-red-700 text-white hover:bg-red-800',
        'inverse' => 'bg-white text-brand-900 hover:bg-brand-100',
    ];

    $classes = $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
