<?php

use App\Models\User;
use Livewire\Livewire;

test('language page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('language.edit'))
        ->assertOk()
        ->assertSeeText('Español')
        ->assertSeeText('English');
});

test('language is saved as soon as it is picked', function () {
    $user = User::factory()->create(['locale' => 'es']);

    $this->actingAs($user);

    Livewire::test('pages::settings.language')
        ->assertSet('locale', 'es')
        ->set('locale', 'en')
        ->assertHasNoErrors()
        ->assertRedirect(route('language.edit'));

    expect($user->refresh()->locale)->toBe('en');
    expect(session('locale'))->toBe('en');

    $this->get(route('language.edit'))->assertSee('<html lang="en"', false);
});

test('language must be a supported one', function () {
    $user = User::factory()->create(['locale' => 'es']);

    $this->actingAs($user);

    Livewire::test('pages::settings.language')
        ->set('locale', 'fr')
        ->assertHasErrors(['locale']);

    expect($user->refresh()->locale)->toBe('es');
});

test('settings navigation lists language after profile and security last', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('language.edit'))->assertSeeTextInOrder(['Profile', 'Language', 'Appearance', 'Security']);
});
