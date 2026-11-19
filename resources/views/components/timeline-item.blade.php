@props([
    'period',
    'role',
    'organization' => null,
    'on' => 'dark',
])

<li {{ $attributes->class([
    'relative flex flex-col gap-1 border-l-2 border-sea pb-10 pl-8 last:pb-0',
]) }}>
    <span aria-hidden="true" @class([
        'absolute top-1 -left-[9px] size-4 rounded-full border-2 border-sea',
        'bg-navy' => $on === 'dark',
        'bg-mist' => $on === 'light',
    ])></span>

    <p @class([
        'font-mono text-xs font-medium',
        'text-ice' => $on === 'dark',
        'text-deep' => $on === 'light',
    ])>{{ $period }}</p>

    <h3 @class([
        'font-display text-xl font-bold',
        'text-white' => $on === 'dark',
        'text-navy' => $on === 'light',
    ])>{{ $role }}</h3>

    @if ($organization)
        <p @class([
            'font-medium',
            'text-mist' => $on === 'dark',
            'text-ink' => $on === 'light',
        ])>{{ $organization }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div @class([
            'mt-2 text-sm',
            'text-muted' => $on === 'dark',
            'text-ink' => $on === 'light',
        ])>{{ $slot }}</div>
    @endif
</li>
