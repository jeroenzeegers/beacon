<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-liquid btn-magnetic ripple-effect w-full inline-flex items-center justify-center gap-2 px-6 py-3 font-semibold text-sm text-white tracking-wide focus-ring']) }}>
    <span class="btn-magnetic-inner inline-flex items-center justify-center gap-2">
        {{ $slot }}
    </span>
</button>
