<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UpdateLocaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('mi-historia', 'story')->name('story');
Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('proyectos', [ProjectController::class, 'index'])->name('projects.index');
Route::get('proyectos/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::post('idioma', UpdateLocaleController::class)->name('locale.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('ui', 'ui')->name('ui');

    Route::livewire('dashboard/projects', 'pages::projects.index')->name('dashboard.projects.index');
    Route::livewire('dashboard/projects/create', 'pages::projects.form')->name('dashboard.projects.create');
    Route::livewire('dashboard/projects/{project}/edit', 'pages::projects.form')->name('dashboard.projects.edit');
});

require __DIR__.'/settings.php';
