<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-red-600/80 border border-red-500/30 rounded-xl font-semibold text-sm text-white hover:bg-red-500 active:bg-red-700 focus:outline-none focus-ring disabled:opacity-25 transition-all duration-300']) }}>
    {{ $slot }}
</button>
