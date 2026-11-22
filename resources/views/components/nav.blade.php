@php
    $homeUrl = route('home');
    $links = collect(__('nav.sections'))->map(fn (array $section, string $key): array => [
        'label' => $section['label'],
        'href' => match ($key) {
            'story' => route('story'),
            'projects' => route('projects.index'),
            'content' => route('blog.index'),
            default => $homeUrl.'#'.$section['anchor'],
        },
        'current' => match ($key) {
            'story' => request()->routeIs('story'),
            'projects' => request()->routeIs('projects.*'),
            'content' => request()->routeIs('blog.*'),
            default => false,
        },
    ]);
@endphp

<header {{ $attributes->class('sticky top-0 z-40 border-b border-line-subtle bg-navy') }}>
    <nav class="mx-auto flex max-w-site items-center justify-between gap-4 px-4 py-[18px] sm:px-6" aria-label="{{ __('nav.label') }}">
        <a href="{{ $homeUrl }}" class="-my-1.5 flex shrink-0 items-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice">
            <img src="{{ asset('images/brand/DelfinBeta_Isotipo_White.png') }}" alt="" width="800" height="450" class="hidden h-10 w-auto sm:block">
            <img src="{{ asset('images/brand/DelfinBeta_Logotipo_White.png') }}" alt="{{ config('app.name') }}" width="1500" height="400" class="h-10 w-auto">
        </a>

        <ul class="hidden items-center gap-7 text-[15px] font-medium lg:flex">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['href'] }}" @if ($link['current']) aria-current="page" @endif @class([
                        'transition-colors hover:text-ice focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice',
                        'text-ice' => $link['current'],
                        'text-mist' => ! $link['current'],
                    ])>{{ $link['label'] }}</a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('locale.update') }}" role="group" aria-label="{{ __('nav.language') }}" class="flex overflow-hidden rounded-btn border border-line-strong font-mono text-[13px] font-medium">
                @csrf
                @foreach (config('site.locales') as $locale)
                    @if ($locale === app()->getLocale())
                        <span aria-current="true" lang="{{ $locale }}" class="bg-ice px-3 py-2.5 text-navy uppercase">{{ $locale }}</span>
                    @else
                        <button type="submit" name="locale" value="{{ $locale }}" lang="{{ $locale }}" aria-label="{{ config("site.locale_names.{$locale}") }}" class="cursor-pointer px-3 py-2.5 text-mist uppercase transition-colors hover:bg-deep">{{ $locale }}</button>
                    @endif
                @endforeach
            </form>

            <x-resume-dropdown class="hidden sm:block" trigger-class="min-h-11 px-[18px] text-[15px]" />

            <details data-dropdown class="group relative lg:hidden">
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
                    @foreach ($links as $link)
                        <li>
                            <a href="{{ $link['href'] }}" @if ($link['current']) aria-current="page" @endif @class([
                                'block rounded-btn px-4 py-3 font-medium hover:bg-deep',
                                'text-ice' => $link['current'],
                                'text-mist' => ! $link['current'],
                            ])>{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                    <li class="mt-1 border-t border-line-dark pt-1 sm:hidden">
                        <p class="px-4 pt-2 pb-1 font-mono text-xs text-mist/70 uppercase">{{ __('nav.download_cv') }}</p>
                        <ul>
                            @foreach (config('site.locales') as $resumeLocale)
                                <li>
                                    <a href="{{ Storage::disk('public')->url(config("site.resumes.{$resumeLocale}")) }}" download hreflang="{{ $resumeLocale }}" lang="{{ $resumeLocale }}" class="block rounded-btn px-4 py-3 font-medium text-ice hover:bg-deep">{{ config("site.locale_names.{$resumeLocale}") }} (PDF)</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </details>
        </div>
    </nav>
</header>
