<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the request locale.
     *
     * Public pages pass their route-group locale (/es, /en), which is also remembered in the session.
     * Unprefixed pages (auth, dashboard, settings) use that remembered locale, then the browser's
     * preferred language, falling back to the first supported locale.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        $supportedLocales = config('site.locales');

        if ($locale !== null) {
            $request->session()->put('locale', $locale);
        } else {
            $rememberedLocale = $request->session()->get('locale');

            $locale = in_array($rememberedLocale, $supportedLocales, true)
                ? $rememberedLocale
                : $request->getPreferredLanguage($supportedLocales);
        }

        App::setLocale($locale);

        return $next($request);
    }
}
