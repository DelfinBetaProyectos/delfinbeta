@props([
    'label' => 'Foto próximamente',
])

<div role="img" aria-label="{{ $label }}" {{ $attributes->class('flex aspect-[4/3] items-center justify-center rounded-card border border-dashed border-line-light bg-[#cfe0ea] p-6 text-center') }}>
    <span class="font-mono text-xs text-ink">{{ $label }}</span>
</div>
