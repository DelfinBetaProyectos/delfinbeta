@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'on' => 'dark',
    'level' => 2,
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
        'font-display text-3xl font-bold tracking-tight md:text-[2.75rem] md:leading-tight',
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
