@props([
    'label' => 'Foto próximamente',
    'variant' => 'light',
])

<div role="img" aria-label="{{ $label }}" {{ $attributes->class([
    'flex items-center justify-center p-6 text-center',
    'aspect-[4/3] rounded-card border border-dashed border-line-light bg-[#cfe0ea]' => $variant === 'light',
    'aspect-video rounded-card border border-dashed border-line-dashed bg-deep-dark' => $variant === 'dark',
    'aspect-[4/5] rounded-[20px] border-2 border-sea bg-deep' => $variant === 'portrait',
]) }}>
    <span @class([
        'font-mono text-[13px]',
        'text-ink' => $variant === 'light',
        'text-muted' => $variant !== 'light',
    ])>{{ $label }}</span>
</div>
