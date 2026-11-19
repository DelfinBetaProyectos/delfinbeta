@props([
    'label' => 'Foto próximamente',
    'variant' => 'light',
    'aspect' => '4/3',
])

<div role="img" aria-label="{{ $label }}" {{ $attributes->class([
    'flex items-center justify-center p-6 text-center',
    match ($aspect) {
        '1/1' => 'aspect-square',
        '4/5' => 'aspect-[4/5]',
        '16/9' => 'aspect-video',
        default => 'aspect-[4/3]',
    },
    match ($variant) {
        'dark' => 'rounded-card border border-dashed border-line-dashed bg-deep-dark',
        'deep' => 'rounded-[20px] border border-dashed border-line-dashed bg-deep',
        'portrait' => 'rounded-[20px] border-2 border-sea bg-deep',
        default => 'rounded-card border border-dashed border-line-light bg-[#cfe0ea]',
    },
]) }}>
    <span @class([
        'max-w-xs font-mono text-[13px]',
        'text-ink' => $variant === 'light',
        'text-muted' => $variant !== 'light',
    ])>{{ $label }}</span>
</div>
