<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSeeText('Already have an account?')
        ->assertSee('href="'.route('login').'"', false);
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    expect(auth()->user())
        ->firstname->toBe('John')
        ->lastname->toBe('Doe')
        ->locale->toBe(app()->getLocale());
});
