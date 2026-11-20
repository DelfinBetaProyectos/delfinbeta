<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocaleRequest;
use Illuminate\Http\RedirectResponse;

class UpdateLocaleController extends Controller
{
    /**
     * Switch the site language from the language picker.
     *
     * The choice is remembered in the session and, for signed-in users, saved to their profile
     * so the dashboard and settings follow it too.
     */
    public function __invoke(UpdateLocaleRequest $request): RedirectResponse
    {
        $locale = $request->validated('locale');

        $request->session()->put('locale', $locale);
        $request->user()?->update(['locale' => $locale]);

        return back();
    }
}
