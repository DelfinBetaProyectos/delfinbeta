<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UpdateLocaleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('mi-historia', 'story')->name('story');
Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('proyectos/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::post('idioma', UpdateLocaleController::class)->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('ui', 'ui')->name('ui');
});

require __DIR__.'/settings.php';
