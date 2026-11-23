<?php

use App\Mail\MonthlyReportMail;
use App\Models\ContactMessage;
use App\Models\GrowthPlan;
use App\Models\User;
use App\Services\MonthlyReport;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo(now()->setDate(2026, 11, 23)->setTime(10, 0));
    app()->setLocale('es');
    GrowthPlan::factory()->create(['starts_on' => '2026-10-17']);
});

it('requires authentication for every download', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with([
    'pdf' => fn () => route('dashboard.reports.monthly', '2026-11'),
    'messages' => fn () => route('dashboard.reports.messages'),
    'visits' => fn () => route('dashboard.reports.visits'),
    'page' => fn () => route('dashboard.reports.index'),
]);

it('downloads the monthly report as a PDF', function () {
    ContactMessage::factory()->project()->create(['created_at' => now()->subDay()]);

    $response = $this->actingAs(User::factory()->create())->get(route('dashboard.reports.monthly', '2026-11'));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertDownload('informe-delfinbeta-2026-11.pdf');

    expect($response->getContent())->toStartWith('%PDF');
});

it('only accepts real months for the PDF', function () {
    $this->actingAs(User::factory()->create())->get('/dashboard/reports/monthly/2026-13')->assertNotFound();
});

it('exports the messages of the period and language as CSV', function () {
    ContactMessage::factory()->responded(5)->create(['name' => 'Ana Pérez', 'created_at' => now()->subDays(2)]);
    ContactMessage::factory()->project()->create(['name' => 'Client Two', 'locale' => 'en', 'created_at' => now()->subDay()]);
    ContactMessage::factory()->create(['name' => 'Mes Pasado', 'created_at' => now()->subMonth()]);

    $this->actingAs(User::factory()->create(['locale' => 'es']));

    $csv = $this->get(route('dashboard.reports.messages', ['period' => '2026-11']))
        ->assertOk()
        ->assertDownload('mensajes-2026-11-01_2026-11-30.csv')
        ->streamedContent();

    expect($csv)->toContain('Ana Pérez')->toContain(',Respondido,5')
        ->toContain('Client Two')->toContain('Enviado a AraguaneyCode')
        ->not->toContain('Mes Pasado');

    expect($this->get(route('dashboard.reports.messages', ['period' => '2026-11', 'locale' => 'en']))->streamedContent())
        ->toContain('Client Two')->not->toContain('Ana Pérez');
});

it('exports the daily visits from Google Analytics as CSV', function () {
    fakeGoogleAnalytics(fn (array $report): array => [[['20261101'], [12, 30]], [['20261102'], [9, 14]]]);

    $csv = $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard.reports.visits', ['period' => '2026-11']))
        ->assertOk()
        ->streamedContent();

    expect($csv)->toContain("2026-11-01,12,30\n")->toContain("2026-11-02,9,14\n");
});

it('cannot export visits without Google Analytics', function () {
    $this->actingAs(User::factory()->create())->get(route('dashboard.reports.visits'))->assertStatus(503);
});

it('emails the previous month report with the PDF to the site address', function () {
    Mail::fake();

    $this->artisan('reports:send-monthly')->assertSuccessful();

    Mail::assertSent(MonthlyReportMail::class, function (MonthlyReportMail $mail): bool {
        return $mail->report->month === '2026-10'
            && $mail->hasTo(config('site.email'))
            && $mail->hasSubject('Informe mensual de octubre de 2026 · delfinbeta.tech');
    });
});

it('rejects a malformed month in the command', function () {
    Mail::fake();

    $this->artisan('reports:send-monthly', ['--month' => 'noviembre'])->assertFailed();

    Mail::assertNothingSent();
});

it('renders the report email with the PDF attached', function () {
    $mail = new MonthlyReportMail(new MonthlyReport('2026-10'));

    $mail->assertSeeInHtml('Informe de octubre de 2026');
    expect($mail->attachments()[0]->as)->toBe('informe-delfinbeta-2026-10.pdf');
});

it('lists the plan months and sends a report on demand', function () {
    Mail::fake();
    $this->actingAs(User::factory()->create(['locale' => 'es']));

    Livewire::test('pages::reports.index')
        ->assertSeeTextInOrder(['noviembre 2026', 'octubre 2026'])
        ->call('send', '2026-10')
        ->assertHasNoErrors();

    Mail::assertSent(MonthlyReportMail::class, fn (MonthlyReportMail $mail): bool => $mail->report->month === '2026-10');
});

it('does not send reports of months outside the plan', function () {
    Mail::fake();
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::reports.index')->call('send', '2025-01')->assertNotFound();

    Mail::assertNothingSent();
});
