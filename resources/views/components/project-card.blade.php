@props([
    'title',
    'summary',
    'label' => null,
    'href' => null,
])

<article {{ $attributes->class('group relative flex flex-col gap-3 rounded-card bg-deep p-6 transition-colors has-[a:hover]:bg-[#00508f]') }}>
    @if ($label)
        <p class="font-mono text-xs font-medium text-muted">{{ $label }}</p>
    @endif

    <h3 class="font-display text-xl font-bold text-white md:text-2xl">
        @if ($href)
            <a href="{{ $href }}" class="after:absolute after:inset-0 after:rounded-card focus-visible:outline-none focus-visible:after:ring-2 focus-visible:after:ring-ice">{{ $title }}</a>
        @else
            {{ $title }}
        @endif
    </h3>

    <p class="text-sm text-muted">{{ $summary }}</p>

    @if ($slot->isNotEmpty())
        <div class="relative mt-2 flex flex-wrap gap-2">{{ $slot }}</div>
    @endif
</article>
