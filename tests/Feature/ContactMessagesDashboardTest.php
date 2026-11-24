<?php

use App\Enums\ContactTopic;
use App\Models\ContactMessage;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create(['locale' => 'es']));
    app()->setLocale('es');
});

it('lists the contact messages newest first', function () {
    ContactMessage::factory()->create(['name' => 'Primera Persona', 'created_at' => now()->subDay()]);
    ContactMessage::factory()->project()->create(['name' => 'Segunda Persona']);

    $this->get(route('dashboard.messages.index'))
        ->assertOk()
        ->assertSeeText('Mensajes')
        ->assertSeeTextInOrder(['Segunda Persona', 'Un proyecto', 'Primera Persona', 'Una charla o evento']);
});

it('shows an empty state when there are no messages', function () {
    $this->get(route('dashboard.messages.index'))->assertOk()->assertSeeText('Todavía no hay mensajes.');
});

it('searches by name, email or message', function (string $search) {
    ContactMessage::factory()->create(['name' => 'Ana Pérez', 'email' => 'ana@example.com', 'message' => 'Charla sobre Laravel en Maracay']);
    ContactMessage::factory()->create(['name' => 'Luis Gómez', 'email' => 'luis@example.com', 'message' => 'Otra consulta distinta']);

    Livewire::test('pages::messages.index')
        ->set('search', $search)
        ->assertSee('Ana Pérez')
        ->assertDontSee('Luis Gómez');
})->with(['name' => 'Pérez', 'email' => 'ana@', 'message' => 'Laravel']);

it('filters by topic and can clear the filters', function () {
    ContactMessage::factory()->project()->create(['name' => 'Cliente Proyecto']);
    ContactMessage::factory()->create(['name' => 'Organizadora Charla']);

    Livewire::test('pages::messages.index')
        ->set('topic', ContactTopic::Project->value)
        ->assertSee('Cliente Proyecto')
        ->assertDontSee('Organizadora Charla')
        ->call('clearFilters')
        ->assertSee('Organizadora Charla');
});

it('paginates the messages', function () {
    ContactMessage::factory()->count(21)->sequence(fn ($sequence) => ['name' => "Persona {$sequence->index}"])->create();

    Livewire::test('pages::messages.index')
        ->assertSee('Persona 20')
        ->assertDontSee('Persona 0')
        ->call('gotoPage', 2)
        ->assertSee('Persona 0');
});

it('opens the full message with a reply link', function () {
    $contactMessage = ContactMessage::factory()->project()->create([
        'name' => 'Ana Pérez',
        'message' => "Primera línea\nSegunda línea del mensaje completo",
    ]);

    Livewire::test('pages::messages.index')
        ->call('openMessage', $contactMessage->id)
        ->assertSet('viewingMessageId', $contactMessage->id)
        ->assertSee('Segunda línea del mensaje completo')
        ->assertSee($contactMessage->reference())
        ->assertSee('Se envió copia a '.config('site.araguaneycode_email'))
        ->assertSee(e($contactMessage->replyMailto()), false);
});

it('keeps the messages behind the login', function () {
    auth()->logout();

    $this->get(route('dashboard.messages.index'))->assertRedirect(route('login'));
});

it('marks a message as read when it is opened', function () {
    $contactMessage = ContactMessage::factory()->create();

    Livewire::test('pages::messages.index')
        ->assertSee('1 sin leer')
        ->call('openMessage', $contactMessage->id)
        ->assertDontSee('1 sin leer');

    expect($contactMessage->refresh()->isRead())->toBeTrue();
});

it('keeps the first read time when a read message is opened again', function () {
    $contactMessage = ContactMessage::factory()->create(['read_at' => now()->subDay()]);

    Livewire::test('pages::messages.index')->call('openMessage', $contactMessage->id);

    expect($contactMessage->refresh()->read_at->isSameDay(now()->subDay()))->toBeTrue();
});

it('marks messages as read and unread from the list', function () {
    $contactMessage = ContactMessage::factory()->create();

    $component = Livewire::test('pages::messages.index')->call('markAsRead', $contactMessage->id);
    expect($contactMessage->refresh()->isRead())->toBeTrue();

    $component->call('markAsUnread', $contactMessage->id);
    expect($contactMessage->refresh()->isRead())->toBeFalse();
});

it('marks every unread message as read at once', function () {
    ContactMessage::factory()->count(3)->create();

    Livewire::test('pages::messages.index')
        ->assertSee('3 sin leer')
        ->call('markAllAsRead')
        ->assertDontSee('sin leer');

    expect(ContactMessage::query()->unread()->count())->toBe(0);
});

it('filters by read status', function () {
    ContactMessage::factory()->create(['name' => 'Mensaje Nuevo']);
    ContactMessage::factory()->read()->create(['name' => 'Mensaje Visto']);

    Livewire::test('pages::messages.index')
        ->set('status', 'unread')
        ->assertSee('Mensaje Nuevo')
        ->assertDontSee('Mensaje Visto')
        ->set('status', 'read')
        ->assertSee('Mensaje Visto')
        ->assertDontSee('Mensaje Nuevo');
});

it('deletes a message after confirming', function () {
    $contactMessage = ContactMessage::factory()->create(['name' => 'Spam Bot']);
    $otherMessage = ContactMessage::factory()->create();

    Livewire::test('pages::messages.index')
        ->call('confirmDelete', $contactMessage->id)
        ->assertSet('deletingMessageId', $contactMessage->id)
        ->call('delete')
        ->assertSet('deletingMessageId', null)
        ->assertDontSee('Spam Bot');

    expect(ContactMessage::find($contactMessage->id))->toBeNull()
        ->and(ContactMessage::find($otherMessage->id))->not->toBeNull();
});

it('shows the unread count as a badge in the dashboard sidebar', function (int $unread, string $badgeStyle) {
    ContactMessage::factory()->count($unread)->create();
    ContactMessage::factory()->read()->create();

    $html = $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('x-data="{ unread: '.$unread.' }"', false)
        ->getContent();

    preg_match('/style="([^"]*)" x-show="unread > 0" data-flux-navlist-badge><span x-text="unread">(\d+)<\/span>/', $html, $badge);

    expect($badge[1])->toBe($badgeStyle)
        ->and($badge[2])->toBe((string) $unread);
})->with([
    'with unread messages' => [3, ';'],
    'without unread messages' => [0, 'display: none;'],
]);

it('updates the sidebar badge when the unread count changes', function () {
    $contactMessage = ContactMessage::factory()->create();
    ContactMessage::factory()->create();

    Livewire::test('pages::messages.index')
        ->call('markAsRead', $contactMessage->id)
        ->assertDispatched('unread-messages-updated', count: 1)
        ->call('markAllAsRead')
        ->assertDispatched('unread-messages-updated', count: 0);
});

it('records and undoes the reply to a message', function () {
    $this->travelTo(now()->setTime(10, 0));
    $contactMessage = ContactMessage::factory()->create(['created_at' => now()->subHours(5)]);

    Livewire::test('pages::messages.index')
        ->call('markAsResponded', $contactMessage->id)
        ->assertSeeText('Respondido');

    expect($contactMessage->refresh()->responseHours())->toBe(5)
        ->and($contactMessage->isRead())->toBeTrue();

    Livewire::test('pages::messages.index')->call('markAsNotResponded', $contactMessage->id);

    expect($contactMessage->refresh()->isResponded())->toBeFalse();
});
