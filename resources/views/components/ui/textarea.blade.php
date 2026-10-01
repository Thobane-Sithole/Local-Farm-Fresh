@props(['name', 'label', 'hint' => null, 'required' => false, 'rows' => 4, 'value' => null])

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
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required aria-required="true" @endif
        @if ($hasError) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->except('id')->merge([
            'class' => 'block w-full rounded-xl border bg-white px-3.5 py-3 text-[0.95rem] text-ink placeholder:text-muted/70 focus:border-brand-500 focus:ring-brand-500 '
                .($hasError ? 'border-red-600' : 'border-line'),
        ]) }}
    >{{ old($name, $value) }}</textarea>
    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-sm text-muted">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>
    @enderror
</div>
