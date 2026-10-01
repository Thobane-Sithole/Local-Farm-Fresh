@props(['tone' => 'dark', 'wordmark' => true])

@php
    $tile = $tone === 'light' ? '#FFFFFF' : '#23823F';
    $leaf = $tone === 'light' ? '#23823F' : '#FFFFFF';
    $dot = $tone === 'light' ? '#43B85F' : '#E6F5E8';
    $text = $tone === 'light' ? 'text-white' : 'text-ink';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    <svg class="h-9 w-9 shrink-0" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
        <rect width="40" height="40" rx="11" fill="{{ $tile }}"/>
        <path d="M10 30c0-11.6 8.4-20 20-20 0 11.6-8.4 20-20 20Z" fill="{{ $leaf }}"/>
        <path d="M10 30 23.5 16.5" stroke="{{ $tile }}" stroke-width="2.4" stroke-linecap="round"/>
        <circle cx="29" cy="29" r="3" fill="{{ $dot }}"/>
    </svg>
    @if ($wordmark)
        <span class="{{ $text }} text-[1.05rem] font-extrabold leading-none tracking-tight">
            Local-Farm-Fresh
        </span>
    @else
        <span class="sr-only">Local-Farm-Fresh</span>
    @endif
</span>
