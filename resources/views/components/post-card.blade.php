@props([
    'title',
    'summary',
    'pillar' => null,
    'date' => null,
    'href' => null,
])

@php
    $publishedAt = $date ? \Illuminate\Support\Facades\Date::parse($date) : null;
@endphp

<article {{ $attributes->class('relative flex flex-col gap-2 rounded-card border border-line-light bg-white p-6 transition-colors has-[a:hover]:border-deep') }}>
    @if ($pillar || $publishedAt)
        <p class="font-mono text-xs font-medium text-deep">
            {{ $pillar }}
            @if ($pillar && $publishedAt)
                <span aria-hidden="true">·</span>
            @endif
            @if ($publishedAt)
                <time datetime="{{ $publishedAt->toDateString() }}">{{ $publishedAt->translatedFormat('j M Y') }}</time>
            @endif
        </p>
    @endif

    <h3 class="font-display text-xl font-bold text-navy">
        @if ($href)
            <a href="{{ $href }}" class="after:absolute after:inset-0 after:rounded-card focus-visible:outline-none focus-visible:after:ring-2 focus-visible:after:ring-deep">{{ $title }}</a>
        @else
            {{ $title }}
        @endif
    </h3>

    <p class="line-clamp-2 text-sm text-ink">{{ $summary }}</p>
</article>
