<?php

use App\Models\User;

it('shows unprefixed pages in the browser language', function (string $acceptLanguage, string $locale, string $text) {
    $response = $this->get(route('login'), ['Accept-Language' => $acceptLanguage]);

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($text);
})->with([
    'spanish browser' => ['es-VE,es;q=0.9', 'es', 'Hola de nuevo'],
    'english browser' => ['en-US,en;q=0.9', 'en', 'Welcome back'],
    'unsupported language' => ['fr-FR,fr;q=0.9', 'es', 'Hola de nuevo'],
]);

it('prefers the language last chosen on the public site over the browser language', function () {
    $this->get('/en')->assertOk();

    $this->get(route('login'), ['Accept-Language' => 'es-VE,es;q=0.9'])
        ->assertOk()
        ->assertSeeText('Welcome back');
});

it('shows the dashboard in the chosen language', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/es')->assertOk();

    $this->get(route('dashboard'), ['Accept-Language' => 'en-US,en;q=0.9'])
        ->assertOk()
        ->assertSee('<html lang="es"', false)
        ->assertSeeText('Plataforma');
});

it('returns validation errors in the chosen language', function () {
    $this->get('/es');

    $this->post(route('login.store'), ['email' => '', 'password' => ''])
        ->assertSessionHasErrors(['email' => 'El campo correo electrónico es obligatorio.']);
});
