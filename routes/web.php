<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\MessageController;

use App\Models\Message;

use App\Mail\ContactMail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::post('/contact', [MessageController::class, 'store'])->name('contact.store');

Route::get('/flowbite', function () {
    return view('layouts/flowbite');
})->name('flowbite');

Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

Route::get('/500', function () {
    return view('500');
})->name('500');

Route::get('/404', function () {
    return view('404');
})->name('404');

/*
|--------------------------------------------------------------------------
| Emails Routes
|--------------------------------------------------------------------------
*/
Route::get('/mail/contact', function () {
    $message = Message::find(1);
 
    return new ContactMail($message);
})->name('contact.mail');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
