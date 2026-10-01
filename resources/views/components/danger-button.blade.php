<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-[46px] items-center justify-center rounded-xl bg-red-700 px-5 text-[0.95rem] font-semibold text-white transition-colors hover:bg-red-800']) }}>
    {{ $slot }}
</button>
