@props([
    'alternateLocales' => [],
])

@php
    $links = collect(__('nav.sections'))
        ->mapWithKeys(fn (array $section): array => [$section['label'] => route('site.home').'#'.$section['anchor']]);
@endphp

<header {{ $attributes->class('sticky top-0 z-40 border-b border-line-dark bg-navy/95 backdrop-blur') }}>
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4 sm:px-6" aria-label="{{ __('nav.label') }}">
        <a href="{{ route('site.home') }}" class="font-display text-xl font-bold text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice">
            delfinbeta<span class="text-sea">.tech</span>
        </a>

        <div class="flex items-center gap-4 md:gap-8">
            <ul class="hidden items-center gap-8 md:flex">
                @foreach ($links as $label => $href)
                    <li>
                        <a href="{{ $href }}" class="text-sm font-medium text-muted transition-colors hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>

            @foreach ($alternateLocales as $locale => $url)
                <a href="{{ $url }}" hreflang="{{ $locale }}" lang="{{ $locale }}" class="rounded-btn border border-line-dark px-3 py-2 font-mono text-xs font-medium text-ice uppercase transition-colors hover:border-ice focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice">
                    <span class="sr-only">{{ __('nav.switch_language') }}:</span> {{ $locale }}
                </a>
            @endforeach

            <details class="group relative md:hidden">
                <summary class="flex size-10 cursor-pointer list-none items-center justify-center rounded-btn border border-line-dark text-mist [&::-webkit-details-marker]:hidden">
                    <span class="sr-only">{{ __('nav.open_menu') }}</span>
                    <svg class="size-5 group-open:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg class="hidden size-5 group-open:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </summary>

                <ul class="absolute right-0 mt-2 flex w-56 flex-col rounded-card border border-line-dark bg-navy p-2 shadow-xl">
                    @foreach ($links as $label => $href)
                        <li>
                            <a href="{{ $href }}" class="block rounded-btn px-4 py-3 font-medium text-mist hover:bg-deep">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </details>
        </div>
    </nav>
</header>
