@props([
    'name',
    'label',
    'options' => [],
    'placeholder' => 'Choose one',
    'required' => false,
    'value' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $selected = (string) old($name, $value);
@endphp

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-semibold text-ink">
        {{ $label }}
        @unless ($required)
            <span class="font-normal text-muted">(optional)</span>
        @endunless
    </label>

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($required) required aria-required="true" @endif
        @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('id')->merge([
            'class' => 'block w-full min-h-[46px] rounded-xl border bg-white px-3.5 text-[0.95rem] text-ink focus:border-brand-500 focus:ring-brand-500 '
                .($hasError ? 'border-red-600' : 'border-line'),
        ]) }}
    >
        <option value="" disabled @selected($selected === '')>{{ $placeholder }}</option>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @error($name)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>
    @enderror
</div>
