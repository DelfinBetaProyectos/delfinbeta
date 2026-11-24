<?php

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

it('lists one row per email with a preview link per language', function () {
    $response = $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard.mails.index'))
        ->assertOk()
        ->assertSeeText('Correos')
        ->assertSeeTextInOrder(['Correo', 'Variante', 'Destinatario', 'Español', 'English']);

    foreach (['es', 'en'] as $locale) {
        $response->assertSee(route('dashboard.mails.contact-confirmation', [$locale, 'project']), false)
            ->assertSee(route('dashboard.mails.password-reset', $locale), false)
            ->assertSee(route('dashboard.mails.verify-email', $locale), false);
    }

    $response->assertSee(route('dashboard.mails.contact-received', 'project'), false)
        ->assertSee('title="No se envía en este idioma"', false);
});

it('renders the contact notification preview', function (string $topic, bool $mentionsAgency) {
    $response = $this->actingAs(User::factory()->create())
        ->get(route('dashboard.mails.contact-received', $topic))
        ->assertOk()
        ->assertSeeText('Nuevo mensaje de Ana');

    $mentionsAgency
        ? $response->assertSee(config('site.araguaneycode_email'))
        : $response->assertDontSee(config('site.araguaneycode_email'));
})->with([
    'any topic' => ['talk', false],
    'project' => ['project', true],
]);

it('renders the contact confirmation preview in each language', function (string $locale, string $greeting) {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.mails.contact-confirmation', [$locale, 'project']))
        ->assertOk()
        ->assertSeeText($greeting)
        ->assertSee('href="'.route('projects.index').'"', false);
})->with([
    'spanish' => ['es', 'Hola, Ana:'],
    'english' => ['en', 'Hi Ana,'],
]);

it('renders the password reset preview in each language for the signed-in user', function (string $locale, string $button) {
    $user = User::factory()->create(['firstname' => 'Dayan']);

    $this->actingAs($user)
        ->get(route('dashboard.mails.password-reset', $locale))
        ->assertOk()
        ->assertSeeText($button)
        ->assertSeeText('Dayan')
        ->assertSee(e(route('password.reset', ['token' => 'preview-token', 'email' => $user->email])), false);
})->with([
    'spanish' => ['es', 'Restablecer contraseña'],
    'english' => ['en', 'Reset Password'],
]);

it('renders the email verification preview in each language for the signed-in user', function (string $locale, string $button) {
    $this->actingAs(User::factory()->create(['firstname' => 'Dayan']))
        ->get(route('dashboard.mails.verify-email', $locale))
        ->assertOk()
        ->assertSeeText($button)
        ->assertSeeText('Dayan')
        ->assertSee('/email/verify/', false);
})->with([
    'spanish' => ['es', 'Verifica tu correo electrónico'],
    'english' => ['en', 'Verify Email Address'],
]);

it('neither saves nor sends anything when previewing', function () {
    Mail::fake();

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.mails.contact-received', 'project'))
        ->assertOk();

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingOutgoing();
});

it('rejects unknown topics and languages', function (string $url) {
    $this->actingAs(User::factory()->create())->get($url)->assertNotFound();
})->with([
    'unknown topic' => ['/dashboard/mails/contact-received/prices'],
    'unknown language' => ['/dashboard/mails/contact-confirmation/fr/talk'],
]);

it('keeps the previews behind the login', function (string $routeName, array $parameters) {
    $this->get(route($routeName, $parameters))->assertRedirect(route('login'));
})->with([
    'list' => ['dashboard.mails.index', []],
    'notification' => ['dashboard.mails.contact-received', ['talk']],
    'confirmation' => ['dashboard.mails.contact-confirmation', ['es', 'talk']],
    'password reset' => ['dashboard.mails.password-reset', ['es']],
    'verify email' => ['dashboard.mails.verify-email', ['es']],
]);
