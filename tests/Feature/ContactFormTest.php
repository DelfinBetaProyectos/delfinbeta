<?php

use App\Enums\ContactTopic;
use App\Mail\ContactConfirmation;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

/**
 * @return array<string, string>
 */
function contactPayload(array $overrides = []): array
{
    return [
        'name' => 'Ana Pérez',
        'email' => 'ana@example.com',
        'topic' => 'talk',
        'message' => "Hola Dayan,\nme gustaría invitarte a una charla.",
        ...$overrides,
    ];
}

beforeEach(fn () => Mail::fake());

it('saves the message and queues both emails', function () {
    $this->withSession(['locale' => 'es'])
        ->from('/')
        ->post(route('contact.store'), contactPayload())
        ->assertRedirect(route('home').'#contacto')
        ->assertSessionHas('contact_sent');

    $contactMessage = ContactMessage::sole();
    expect($contactMessage)
        ->topic->toBe(ContactTopic::Talk)
        ->locale->toBe('es')
        ->page->toBe('/#contacto');

    Mail::assertQueued(ContactReceived::class, fn (ContactReceived $mail): bool => $mail->hasTo(config('site.email'))
        && $mail->hasReplyTo('ana@example.com', 'Ana Pérez')
        && ! $mail->hasCc(config('site.araguaneycode_email'))
        && $mail->locale === 'es');
    Mail::assertQueued(ContactConfirmation::class, fn (ContactConfirmation $mail): bool => $mail->hasTo('ana@example.com')
        && $mail->hasReplyTo(config('site.email')));
});

it('copies project inquiries to AraguaneyCode and mentions it in both emails', function () {
    $this->post(route('contact.store'), contactPayload(['topic' => 'project']))->assertRedirect();

    Mail::assertQueued(ContactReceived::class, function (ContactReceived $mail): bool {
        $mail->assertSeeInHtml(config('site.araguaneycode_email'));

        return $mail->hasCc(config('site.araguaneycode_email'));
    });
    Mail::assertQueued(ContactConfirmation::class, function (ContactConfirmation $mail): bool {
        $mail->assertSeeInHtml(config('site.araguaneycode_url'));

        return true;
    });
});

it('leaves the AraguaneyCode note out of other topics', function () {
    $contactMessage = ContactMessage::factory()->create();

    (new ContactReceived($contactMessage))->assertDontSeeInHtml(config('site.araguaneycode_email'));
    (new ContactConfirmation($contactMessage))->assertDontSeeInHtml('AraguaneyCode</a>', false);
});

it('confirms in the language the sender used on the site', function () {
    $this->withSession(['locale' => 'en'])->post(route('contact.store'), contactPayload())->assertRedirect(route('home').'#contact');

    Mail::assertQueued(ContactConfirmation::class, function (ContactConfirmation $mail): bool {
        $mail->assertHasSubject('Got your message, Ana')
            ->assertSeeInHtml('will reply personally within 48 hours');

        return $mail->locale === 'en';
    });
    Mail::assertQueued(ContactReceived::class, function (ContactReceived $mail): bool {
        $mail->assertHasSubject('[Charla] Nuevo mensaje de Ana · delfinbeta.tech')
            ->assertSeeInHtml('Versión en inglés');

        return $mail->locale === 'es';
    });
});

it('keeps the line breaks and escapes the message in the emails', function () {
    $contactMessage = ContactMessage::factory()->create(['message' => "Primera línea\n<script>alert(1)</script>"]);

    (new ContactConfirmation($contactMessage))
        ->assertSeeInHtml('Primera línea<br>', false)
        ->assertDontSeeInHtml('<script>alert(1)</script>', false);
});

it('silently drops submissions that fill the honeypot', function () {
    $this->withSession(['locale' => 'es'])
        ->post(route('contact.store'), contactPayload(['website' => 'https://spam.example']))
        ->assertRedirect(route('home').'#contacto')
        ->assertSessionHas('contact_sent');

    expect(ContactMessage::count())->toBe(0);
    Mail::assertNothingQueued();
});

it('validates the form and returns to the contact section', function (array $overrides, string $field) {
    $this->withSession(['locale' => 'es'])
        ->from('/')
        ->post(route('contact.store'), contactPayload($overrides))
        ->assertRedirect(route('home').'#contacto')
        ->assertSessionHasErrors($field);

    Mail::assertNothingQueued();
})->with([
    'missing name' => [['name' => ''], 'name'],
    'invalid email' => [['email' => 'no-es-correo'], 'email'],
    'unknown topic' => [['topic' => 'prices'], 'topic'],
    'short message' => [['message' => 'Hola'], 'message'],
]);

it('rate limits the form per visitor', function () {
    foreach (range(1, 3) as $attempt) {
        $this->post(route('contact.store'), contactPayload())->assertSessionHasNoErrors();
    }

    $this->withSession(['locale' => 'es'])
        ->from('/')
        ->post(route('contact.store'), contactPayload())
        ->assertRedirect(route('home').'#contacto')
        ->assertSessionHasErrors(['message' => __('home.contact.form.throttled', [], 'es')]);

    expect(ContactMessage::count())->toBe(3);
});

it('shows the thank-you message after sending', function () {
    $this->withSession(['locale' => 'es', 'contact_sent' => true])->get('/')
        ->assertOk()
        ->assertSeeText('¡Gracias por escribirme!')
        ->assertSeeText('en menos de 48 horas');
});

it('retries the emails when a delivery attempt fails or hangs', function (string $mailable) {
    $mail = new $mailable(ContactMessage::factory()->create());

    expect($mail)
        ->tries->toBe(3)
        ->backoff->toBe([10, 60])
        ->timeout->toBeLessThan(60);
})->with([ContactReceived::class, ContactConfirmation::class]);
