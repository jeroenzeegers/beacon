@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4']) }}>
        {{ $status }}
    </div>
@endif
