@props(['title', 'action' => null, 'actionHref' => null])

<div {{ $attributes->merge(['class' => 'rounded-card border border-dashed border-line bg-white px-6 py-10 text-center']) }}>
    <svg class="mx-auto h-10 w-10 text-brand-300" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <path d="M8 32c0-13.3 10.7-24 24-24 0 13.3-10.7 24-24 24Z" stroke="currentColor" stroke-width="2.5"/>
        <path d="M8 32 24 16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
    <h3 class="mt-3 text-base font-bold text-ink">{{ $title }}</h3>
    @if (trim($slot))
        <p class="mx-auto mt-1 max-w-sm text-sm text-muted">{{ $slot }}</p>
    @endif
    @if ($action && $actionHref)
        <x-ui.button :href="$actionHref" class="mt-5">{{ $action }}</x-ui.button>
    @endif
</div>
