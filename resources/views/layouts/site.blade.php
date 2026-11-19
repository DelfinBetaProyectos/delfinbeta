@php
    $currentRoute = request()->route();
    $routeLocale = \Illuminate\Support\Str::before((string) $currentRoute?->getName(), '.');
    $localeUrls = in_array($routeLocale, config('site.locales'), true)
        ? collect(config('site.locales'))->mapWithKeys(fn (string $locale): array => [
            $locale => URL::localizedRoute(
                \Illuminate\Support\Str::after($currentRoute->getName(), '.'),
                \Illuminate\Support\Arr::only($currentRoute->parameters(), $currentRoute->parameterNames()),
                $locale,
            ),
        ])
        : collect();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')

        @if ($localeUrls->isNotEmpty())
            @foreach ($localeUrls as $locale => $url)
                <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}" />
            @endforeach
            <link rel="alternate" hreflang="x-default" href="{{ route('home') }}" />
        @endif
    </head>
    <body class="flex min-h-screen flex-col bg-navy font-sans text-[17px] leading-relaxed text-white antialiased">
        <x-nav :locale-urls="$localeUrls" />

        <main class="flex-1">
            {{ $slot }}
        </main>

        <x-footer />
    </body>
</html>
