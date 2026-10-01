@props([
    'name',
    'label',
    'type' => 'text',
    'hint' => null,
    'required' => false,
    'value' => null,
    'toggleable' => false,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $describedBy = collect([$hint ? "{$id}-hint" : null, $hasError ? "{$id}-error" : null])->filter()->implode(' ');
    $isPassword = $type === 'password';
    $useToggle = $isPassword && $toggleable;
@endphp

<div @if($useToggle) x-data="{ show: false }" @endif>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-semibold text-ink">
        {{ $label }}
        @unless ($required)
            <span class="font-normal text-muted">(optional)</span>
        @endunless
    </label>

    <div class="{{ $useToggle ? 'relative' : '' }}">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            @if($useToggle)
                :type="show ? 'text' : 'password'"
            @else
                type="{{ $type }}"
            @endif
            @if ($isPassword) @else value="{{ old($name, $value) }}" @endif
            @if ($required) required aria-required="true" @endif
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->except('id')->merge([
                'class' => 'block w-full min-h-[46px] rounded-xl border bg-white px-3.5 text-[0.95rem] text-ink placeholder:text-muted/70 focus:border-brand-500 focus:ring-brand-500 '
                    .($useToggle ? 'pr-11 ' : '')
                    .($hasError ? 'border-red-600' : 'border-line'),
            ]) }}
        >

        @if($useToggle)
        <button type="button"
            @click="show = !show"
            :aria-label="show ? 'Hide password' : 'Show password'"
            class="absolute inset-y-0 right-0 flex items-center px-3 text-muted hover:text-ink focus:outline-none">
            {{-- Eye open --}}
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            {{-- Eye closed --}}
            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21" />
            </svg>
        </button>
        @endif
    </div>

    @if ($hint)
        <p id="{{ $id }}-hint" class="mt-1.5 text-sm text-muted">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>
    @enderror
</div>
