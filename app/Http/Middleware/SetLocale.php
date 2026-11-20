<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the visitor's language to every page.
     *
     * Uses the signed-in user's saved language, then the language picked on the site (remembered in
     * the session), then the browser's preferred language, falling back to the first supported locale.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('site.locales');
        $userLocale = $request->user()?->locale;
        $rememberedLocale = $request->session()->get('locale');

        $locale = match (true) {
            in_array($userLocale, $supportedLocales, true) => $userLocale,
            in_array($rememberedLocale, $supportedLocales, true) => $rememberedLocale,
            default => $request->getPreferredLanguage($supportedLocales),
        };

        App::setLocale($locale);

        return $next($request);
    }
}
