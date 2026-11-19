@props([
    'period',
    'role',
    'organization' => null,
    'on' => 'light',
])

<li {{ $attributes->class('flex flex-col pb-6 pl-6 last:pb-0') }}>
    <span @class([
        'font-mono text-[13px] font-medium',
        'text-ice' => $on === 'dark',
        'text-deep' => $on === 'light',
    ])>{{ $period }}</span>

    <p @class([
        'mt-0.5 font-medium',
        'text-white' => $on === 'dark',
        'text-navy' => $on === 'light',
    ])>{{ $role }}</p>

    @if ($organization)
        <p @class([
            'text-sm',
            'text-muted' => $on === 'dark',
            'text-ink' => $on === 'light',
        ])>{{ $organization }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div @class([
            'mt-1 text-sm',
            'text-muted' => $on === 'dark',
            'text-ink' => $on === 'light',
        ])>{{ $slot }}</div>
    @endif
</li>
