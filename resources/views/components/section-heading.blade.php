@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'on' => 'dark',
    'level' => 2,
    'size' => 'lg',
])

<header {{ $attributes->class('flex max-w-2xl flex-col gap-3') }}>
    @if ($eyebrow)
        <p @class([
            'font-mono text-sm font-medium',
            'text-ice' => $on === 'dark',
            'text-deep' => $on === 'light',
        ])>{{ $eyebrow }}</p>
    @endif

    <h{{ $level }} @class([
        'font-display text-3xl leading-[1.1] font-bold tracking-[-0.02em]',
        'md:text-[2.75rem]' => $size === 'lg',
        'md:text-[2.5rem]' => $size === 'md',
        'text-white' => $on === 'dark',
        'text-navy' => $on === 'light',
    ])>{{ $title }}</h{{ $level }}>

    @if ($description)
        <p @class([
            'text-lg',
            'text-muted' => $on === 'dark',
            'text-ink' => $on === 'light',
        ])>{{ $description }}</p>
    @endif
</header>
