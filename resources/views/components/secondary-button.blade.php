<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 border border-slate-600 rounded-xl font-semibold text-sm text-slate-300 bg-slate-800/50 hover:bg-slate-700/50 hover:border-slate-500 focus:outline-none focus-ring disabled:opacity-25 transition-all duration-300']) }}>
    {{ $slot }}
</button>
