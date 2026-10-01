@props([
    'name',
    'label',
    'type' => 'text',
    'hint' => null,
    'required' => false,
    'value' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $hasError ? "{$id}-error" : null])->filter()->implode(' ');
@endphp

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-semibold text-ink">
        {{ $label }}
        @unless ($required)
            <span class="font-normal text-muted">(optional)</span>
        @endunless
    </label>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @if ($required) required aria-required="true" @endif
        @if ($hasError) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->except('id')->merge([
            'class' => 'block w-full min-h-[46px] rounded-xl border bg-white px-3.5 text-[0.95rem] text-ink placeholder:text-muted/70 focus:border-brand-500 focus:ring-brand-500 '
                .($hasError ? 'border-red-600' : 'border-line'),
        ]) }}
    >

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-sm text-muted">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>
    @enderror
</div>
