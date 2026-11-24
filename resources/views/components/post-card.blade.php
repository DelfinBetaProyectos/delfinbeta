@props([
    'title',
    'summary',
    'category' => null,
    'date' => null,
    'href' => null,
    'linkText' => null,
])

@php
    $publishedAt = $date ? \Illuminate\Support\Facades\Date::parse($date) : null;
@endphp

<article {{ $attributes->class('flex flex-col gap-3 rounded-card border border-line-light bg-white p-7') }}>
    @if ($category || $publishedAt)
        <p class="font-mono text-[13px] font-medium text-deep">
            {{ $category }}
            @if ($category && $publishedAt)
                <span aria-hidden="true">·</span>
            @endif
            @if ($publishedAt)
                <time datetime="{{ $publishedAt->toDateString() }}">{{ $publishedAt->translatedFormat('j M Y') }}</time>
            @endif
        </p>
    @endif

    <h3 class="font-display text-[1.375rem] leading-tight font-bold text-navy">{{ $title }}</h3>

    <p class="line-clamp-2 text-[15px] text-ink">{{ $summary }}</p>

    @if ($href)
        <a href="{{ $href }}" class="mt-auto font-medium text-deep underline underline-offset-4 hover:no-underline">
            {{ $linkText ?? __('components.read') }} <span aria-hidden="true">→</span>
        </a>
    @endif
</article>
