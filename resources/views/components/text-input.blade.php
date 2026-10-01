@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'min-h-[46px] rounded-xl border-line bg-white px-3.5 text-[0.95rem] text-ink focus:border-brand-500 focus:ring-brand-500']) }}>
