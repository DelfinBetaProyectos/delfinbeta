@props([
    'localeUrls' => [],
])

@php
    $links = collect(__('nav.sections'))
        ->mapWithKeys(fn (array $section): array => [$section['label'] => route('site.home').'#'.$section['anchor']]);
    $contactUrl = route('site.home').'#'.__('nav.sections.contact.anchor');
@endphp

<header {{ $attributes->class('sticky top-0 z-40 border-b border-line-subtle bg-navy') }}>
    <nav class="mx-auto flex max-w-site items-center justify-between gap-4 px-4 py-[18px] sm:px-6" aria-label="{{ __('nav.label') }}">
        <a href="{{ route('site.home') }}" class="font-display text-[22px] font-bold tracking-[-0.01em] text-mist focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice">
            delfinbeta<span class="text-ice">.tech</span>
        </a>

        <ul class="hidden items-center gap-7 text-[15px] font-medium lg:flex">
            @foreach ($links as $label => $href)
                <li>
                    <a href="{{ $href }}" class="text-mist transition-colors hover:text-ice focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice">{{ $label }}</a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-3">
            @if (count($localeUrls) > 1)
                <div role="group" aria-label="{{ __('nav.language') }}" class="flex overflow-hidden rounded-btn border border-line-strong font-mono text-[13px] font-medium">
                    @foreach ($localeUrls as $locale => $url)
                        @if ($locale === app()->getLocale())
                            <a href="{{ $url }}" aria-current="page" lang="{{ $locale }}" class="bg-ice px-3 py-2.5 text-navy uppercase">{{ $locale }}</a>
                        @else
                            <a href="{{ $url }}" hreflang="{{ $locale }}" lang="{{ $locale }}" class="px-3 py-2.5 text-mist uppercase transition-colors hover:bg-deep">{{ $locale }}</a>
                        @endif
                    @endforeach
                </div>
            @endif

            <a href="{{ $contactUrl }}" class="hidden min-h-11 items-center rounded-btn border border-ice px-[18px] text-[15px] font-medium text-ice transition-colors hover:bg-ice/10 hover:text-mist sm:inline-flex">
                {{ __('nav.download_cv') }}
            </a>

            <details class="group relative lg:hidden">
                <summary class="flex size-11 cursor-pointer list-none items-center justify-center rounded-btn border border-line-strong text-mist [&::-webkit-details-marker]:hidden">
                    <span class="sr-only">{{ __('nav.open_menu') }}</span>
                    <svg class="size-5 group-open:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg class="hidden size-5 group-open:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </summary>

                <ul class="absolute right-0 mt-2 flex w-60 flex-col rounded-card border border-line-dark bg-navy p-2 shadow-xl">
                    @foreach ($links as $label => $href)
                        <li>
                            <a href="{{ $href }}" class="block rounded-btn px-4 py-3 font-medium text-mist hover:bg-deep">{{ $label }}</a>
                        </li>
                    @endforeach
                    <li class="sm:hidden">
                        <a href="{{ $contactUrl }}" class="block rounded-btn px-4 py-3 font-medium text-ice hover:bg-deep">{{ __('nav.download_cv') }}</a>
                    </li>
                </ul>
            </details>
        </div>
    </nav>
</header>
