{{--
    Google Analytics 4 on the public site, only when a measurement ID is configured and the visitor
    is not the signed-in owner. Every hit carries the site language (`site_locale`, registered in
    GA4 as an event-scoped custom dimension) so the dashboard can filter by language.
--}}
@php($measurementId = config('services.google_analytics.measurement_id'))
@if (filled($measurementId) && auth()->guest())
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $measurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', @js($measurementId), { site_locale: @js(app()->getLocale()) });
    </script>
@endif
