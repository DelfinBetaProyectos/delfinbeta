<?php

use App\Http\Controllers\RedirectToPreferredLocaleController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', RedirectToPreferredLocaleController::class)->name('home');

Route::prefix('{locale}')
    ->whereIn('locale', config('site.locales'))
    ->middleware(SetLocale::class)
    ->name('site.')
    ->group(function () {
        Route::view('/', 'home')->name('home');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('ui', 'ui')->name('ui');
});

require __DIR__.'/settings.php';
