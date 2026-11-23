<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\Dashboard\MailPreviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UpdateLocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('mi-historia', 'story')->name('story');
Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{article}', [BlogController::class, 'show'])->name('blog.show');
Route::get('proyectos', [ProjectController::class, 'index'])->name('projects.index');
Route::get('proyectos/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::post('contacto', ContactMessageController::class)->middleware('throttle:contact')->name('contact.store');
Route::post('idioma', UpdateLocaleController::class)->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('ui', 'ui')->name('ui');

    Route::livewire('dashboard/projects', 'pages::projects.index')->name('dashboard.projects.index');
    Route::livewire('dashboard/projects/create', 'pages::projects.form')->name('dashboard.projects.create');
    Route::livewire('dashboard/projects/{project}/edit', 'pages::projects.form')->name('dashboard.projects.edit');

    Route::livewire('dashboard/articles', 'pages::articles.index')->name('dashboard.articles.index');
    Route::livewire('dashboard/articles/create', 'pages::articles.form')->name('dashboard.articles.create');
    Route::livewire('dashboard/articles/{article}/edit', 'pages::articles.form')->name('dashboard.articles.edit');

    Route::livewire('dashboard/messages', 'pages::messages.index')->name('dashboard.messages.index');

    Route::livewire('dashboard/mails', 'pages::mails.index')->name('dashboard.mails.index');
    Route::prefix('dashboard/mails')->name('dashboard.mails.')->controller(MailPreviewController::class)->group(function () {
        Route::get('contact-received/{topic}', 'contactReceived')->name('contact-received');
        Route::get('contact-confirmation/{locale}/{topic}', 'contactConfirmation')->whereIn('locale', config('site.locales'))->name('contact-confirmation');
        Route::get('password-reset/{locale}', 'passwordReset')->whereIn('locale', config('site.locales'))->name('password-reset');
        Route::get('verify-email/{locale}', 'verifyEmail')->whereIn('locale', config('site.locales'))->name('verify-email');
    });
});

require __DIR__.'/settings.php';
