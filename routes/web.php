<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RedirectToPreferredLocaleController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', RedirectToPreferredLocaleController::class)->name('home');

foreach (config('site.locales') as $locale) {
    Route::prefix($locale)
        ->middleware(SetLocale::class.':'.$locale)
        ->name("{$locale}.")
        ->group(function () use ($locale) {
            Route::view('/', 'home')->name('home');
            Route::view(trans('routes.story', locale: $locale), 'story')->name('story');
            Route::get(trans('routes.projects', locale: $locale).'/{project}', [ProjectController::class, 'show'])->name('projects.show');
        });
}

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('ui', 'ui')->name('ui');
});

require __DIR__.'/settings.php';
