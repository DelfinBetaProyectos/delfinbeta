@php
    $currentRoute = request()->route();
    $localeUrls = $currentRoute?->hasParameter('locale')
        ? collect(config('site.locales'))->mapWithKeys(fn (string $locale): array => [
            $locale => route($currentRoute->getName(), [
                ...\Illuminate\Support\Arr::only($currentRoute->parameters(), $currentRoute->parameterNames()),
                'locale' => $locale,
            ]),
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
    <body class="flex min-h-screen flex-col bg-navy font-sans text-mist antialiased">
        <x-nav :alternate-locales="$localeUrls->except(app()->getLocale())" />

        <main class="flex-1">
            {{ $slot }}
        </main>

        <x-footer />
    </body>
</html>
