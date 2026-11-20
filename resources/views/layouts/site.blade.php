<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')

        @php
            $metaTitle = filled($title ?? null) ? $title.' - '.config('app.name') : config('app.name');
            $metaDescription = $description ?? __('home.hero.lead');
            $ogImage = asset('images/brand/DelfinBeta_OG.png');
        @endphp
        <meta name="description" content="{{ $metaDescription }}" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="{{ config('app.name') }}" />
        <meta property="og:title" content="{{ $metaTitle }}" />
        <meta property="og:description" content="{{ $metaDescription }}" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:locale" content="{{ app()->getLocale() === 'es' ? 'es_VE' : 'en_US' }}" />
        <meta property="og:image" content="{{ $ogImage }}" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:image:alt" content="{{ config('app.name') }}" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $metaTitle }}" />
        <meta name="twitter:description" content="{{ $metaDescription }}" />
        <meta name="twitter:image" content="{{ $ogImage }}" />
    </head>
    <body class="flex min-h-screen flex-col bg-navy font-sans text-[17px] leading-relaxed text-white antialiased">
        <x-nav />

        <main class="flex-1">
            {{ $slot }}
        </main>

        <x-footer />
    </body>
</html>
