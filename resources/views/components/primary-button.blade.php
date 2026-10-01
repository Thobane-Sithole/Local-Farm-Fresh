<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-[46px] items-center justify-center rounded-xl bg-brand-600 px-5 text-[0.95rem] font-semibold text-white transition-colors hover:bg-brand-800 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
