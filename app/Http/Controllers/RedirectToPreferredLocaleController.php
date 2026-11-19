<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectToPreferredLocaleController extends Controller
{
    /**
     * Send visitors of `/` to the home page in their browser's language, falling back to the first supported locale.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $locale = $request->getPreferredLanguage(config('site.locales'));

        return redirect()->route("{$locale}.home");
    }
}
