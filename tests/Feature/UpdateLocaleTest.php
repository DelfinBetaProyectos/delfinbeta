<?php

use App\Models\User;

it('switches the public pages to the chosen language', function () {
    $this->from('/blog')
        ->post(route('locale.update'), ['locale' => 'en'])
        ->assertRedirect('/blog')
        ->assertSessionHas('locale', 'en');

    $this->get('/blog', ['Accept-Language' => 'es-VE,es;q=0.9'])
        ->assertOk()
        ->assertSee('<html lang="en"', false);
});

it('saves the chosen language to the signed-in user', function () {
    $user = User::factory()->create(['locale' => 'es']);

    $this->actingAs($user)
        ->post(route('locale.update'), ['locale' => 'en'])
        ->assertRedirect();

    expect($user->refresh()->locale)->toBe('en');
});

it('shows public pages in the signed-in user language over the session and browser', function () {
    $this->actingAs(User::factory()->create(['locale' => 'en']))
        ->withSession(['locale' => 'es'])
        ->get('/mi-historia', ['Accept-Language' => 'es-VE,es;q=0.9'])
        ->assertOk()
        ->assertSee('<html lang="en"', false)
        ->assertSeeText('Every stage left something I still use');
});

it('rejects an unsupported language', function () {
    $user = User::factory()->create(['locale' => 'es']);

    $this->actingAs($user)
        ->post(route('locale.update'), ['locale' => 'fr'])
        ->assertSessionHasErrors('locale');

    expect($user->refresh()->locale)->toBe('es');
});

it('offers the other language in the navigation switcher', function () {
    $this->withSession(['locale' => 'es'])
        ->get('/')
        ->assertSee('action="'.route('locale.update').'"', false)
        ->assertSee('name="locale" value="en"', false);
});
