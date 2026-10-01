<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex min-h-[46px] items-center justify-center rounded-xl bg-white px-5 text-[0.95rem] font-semibold text-brand-600 ring-1 ring-inset ring-line transition-colors hover:bg-brand-50 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
