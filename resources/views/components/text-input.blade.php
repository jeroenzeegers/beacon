@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input-liquid w-full']) }}>
