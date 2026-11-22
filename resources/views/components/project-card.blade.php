@props([
    'title',
    'summary',
    'label' => null,
    'image' => null,
    'href' => null,
    'linkText' => null,
])

<article {{ $attributes->class('flex flex-col overflow-hidden rounded-card border border-line-subtle bg-deep') }}>
    <div @class(['flex aspect-video items-center justify-center bg-deep-dark', 'p-6' => ! $image])>
        @if ($image)
            <img src="{{ Storage::disk('public')->url($image) }}" alt="{{ $title }}" loading="lazy" class="size-full object-cover object-top">
        @else
            <p class="font-mono text-[13px] text-muted">{{ __('components.capture_placeholder') }}</p>
        @endif
    </div>

    <div class="flex grow flex-col gap-3 p-7">
        @if ($label)
            <p class="font-mono text-[13px] font-medium text-ice">{{ $label }}</p>
        @endif

        <h3 class="font-display text-[1.625rem] leading-tight font-bold text-white">{{ $title }}</h3>

        <p class="text-muted">{{ $summary }}</p>

        @if ($href)
            <a href="{{ $href }}" class="mt-auto pt-2 font-medium text-ice underline underline-offset-4 hover:text-mist">
                {{ $linkText ?? __('components.view_case') }} <span aria-hidden="true">→</span>
            </a>
        @endif
    </div>
</article>
