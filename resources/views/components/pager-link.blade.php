@props([
    'href',
    'label',
    'title',
    'direction' => 'next',
])

<a href="{{ $href }}" rel="{{ $direction === 'previous' ? 'prev' : 'next' }}" {{ $attributes->class([
    'flex flex-[1_1_20rem] flex-col gap-1.5 rounded-card border border-line-dark p-7 text-mist transition-colors hover:border-ice focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice',
    'items-end text-right' => $direction === 'next',
]) }}>
    <span class="font-mono text-[13px] text-muted">
        @if ($direction === 'previous')
            <span aria-hidden="true">←</span> {{ $label }}
        @else
            {{ $label }} <span aria-hidden="true">→</span>
        @endif
    </span>
    <span class="font-display text-2xl font-bold">{{ $title }}</span>
</a>
