@props([
    'title',
    'summary',
    'meta' => null,
])

<article {{ $attributes->class('flex flex-col gap-2 rounded-xl border border-line-dark bg-navy p-6') }}>
    @if ($meta)
        <p class="font-mono text-[13px] font-medium text-ice">{{ $meta }}</p>
    @endif

    <h3 class="font-display text-[1.375rem] leading-tight font-bold text-white">{{ $title }}</h3>

    <p class="text-[15px] text-muted">{{ $summary }}</p>
</article>
