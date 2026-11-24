<?php

use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\GrowthPlan;
use App\Models\MonthlyMetric;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function () {
    // Day 38 of the plan that started on 17 Oct 2026.
    $this->travelTo(now()->setDate(2026, 11, 23)->setTime(10, 0));
    app()->setLocale('es');
    $this->plan = GrowthPlan::factory()->create(['name' => 'Plan 1', 'starts_on' => '2026-10-17']);
});

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

it('shows the plan day, the next review and that Google Analytics is not connected', function () {
    $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Estadísticas de la marca personal')
        ->assertSeeText('Plan 1 · Día 38 de 90')
        ->assertSeeText('Próxima revisión mensual: domingo 6 de diciembre')
        ->assertSeeText('Google Analytics sin conectar')
        ->assertSeeText('Conecta Google Analytics para ver las visitas.');
});

it('summarizes the messages of the period with their reply status', function () {
    ContactMessage::factory()->project()->create(['name' => 'Cliente Uno', 'created_at' => now()->subDays(3)]);
    ContactMessage::factory()->project()->create(['name' => 'Client Two', 'locale' => 'en', 'created_at' => now()->subDays(2)]);
    ContactMessage::factory()->responded(6)->create(['name' => 'Organizadora', 'created_at' => now()->subDays(5)]);
    ContactMessage::factory()->create(['name' => 'Reclutadora', 'topic' => 'role', 'created_at' => now()->subHours(10)]);
    ContactMessage::factory()->create(['name' => 'Olvidado', 'topic' => 'other', 'created_at' => now()->subMonth()]);

    $this->actingAs(User::factory()->create(['locale' => 'es']));

    Livewire::test('pages::dashboard')
        ->assertSeeText('3 de 4 respondidos en menos de 48 h')
        ->assertSeeText('4 mensajes · 2 enviados a AraguaneyCode')
        ->assertSeeText('3 en español · 1 en inglés')
        ->assertSeeText('6 horas')
        ->assertSeeText('Respondido en 6 h')
        ->assertSeeText('Nuevo · quedan 38 h')
        ->assertSeeText('Plazo vencido')
        ->assertSeeText('Enviado a AraguaneyCode');
});

it('filters the figures by language and period', function () {
    ContactMessage::factory()->create(['created_at' => now()->subDay()]);
    ContactMessage::factory()->project()->create(['locale' => 'en', 'created_at' => now()->subDay()]);
    ContactMessage::factory()->responded(60)->create(['created_at' => now()->subMonthNoOverflow()->startOfMonth()]);

    $this->actingAs(User::factory()->create(['locale' => 'es']));

    Livewire::test('pages::dashboard')
        ->assertSeeText('1 de 2 respondidos en menos de 48 h')
        ->assertSeeText('1 en español · 1 en inglés')
        ->set('locale', 'en')
        ->assertSeeText('1 de 1 respondidos en menos de 48 h')
        ->assertSeeText('0 en español · 1 en inglés')
        ->set('locale', '')
        ->set('period', 'last_month')
        ->assertSeeText('0 de 1 respondidos en menos de 48 h')
        ->assertSeeText('60 horas');
});

it('falls back to the default filters when they are not allowed', function () {
    $this->actingAs(User::factory()->create());

    Livewire::withQueryParams(['period' => 'forever', 'locale' => 'fr'])
        ->test('pages::dashboard')
        ->assertSet('period', 'this_month')
        ->assertSet('locale', '')
        ->set('weeks', 50)
        ->assertSet('weeks', 4);
});

it('adds up the plan figures entered by hand against the goals', function () {
    MonthlyMetric::factory()->for($this->plan)->create(['month' => '2026-10-01', 'publications' => 4, 'linkedin_followers' => 50, 'talks' => 0, 'applications' => 2]);
    MonthlyMetric::factory()->for($this->plan)->create(['month' => '2026-11-01', 'publications' => 5, 'linkedin_followers' => 62, 'talks' => 0, 'applications' => 4]);
    $otherPlan = GrowthPlan::factory()->create(['starts_on' => '2026-06-01']);
    MonthlyMetric::factory()->for($otherPlan)->create(['month' => '2026-08-01', 'publications' => 99]);

    $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard'))
        ->assertSeeText('día 38: 42 %')
        ->assertSeeTextInOrder(['Publicaciones', '9 / 24', 'Seguidores nuevos en LinkedIn', '112 / 300', 'Charlas', '0 / 1', 'Postulaciones o reclutadores', '6 / 15']);
});

it('shows visits, sources, pages, clicks and content performance from Google Analytics', function () {
    $article = Article::factory()->create(['slug' => 'claude-code-real', 'title' => ['es' => 'Claude Code en un proyecto real', 'en' => 'Claude Code in a real project']]);

    fakeGoogleAnalytics(function (array $report): array {
        $isClickReport = str_contains(json_encode($report['dimensionFilter'] ?? []), 'araguaneycode_click');
        $isResumeReport = str_contains(json_encode($report['dimensionFilter'] ?? []), 'resume_download');

        return match (true) {
            reportFields($report) === ['sessionSource'] => [[['linkedin.com'], [100]], [['google'], [141]], [['(direct)'], [96]], [['lnkd.in'], [82]]],
            reportFields($report) === ['pagePath'] && $isClickReport => [[['/blog/claude-code-real'], [5]]],
            reportFields($report) === ['pagePath'] && isset($report['dimensionFilter']) => [[['/blog/claude-code-real'], [58, 500, 2]]],
            reportFields($report) === ['pagePath'] => [[['/mi-historia'], [61]], [['/blog/claude-code-real'], [58]]],
            reportFields($report) === ['customEvent:resume_locale'] && $isResumeReport => [[['es'], [12]], [['en'], [6]]],
            reportFields($report) === ['country'] && $isResumeReport => [[['Venezuela'], [12]], [['Spain'], [5]], [['(not set)'], [1]]],
            reportFields($report) === ['date'] => [[['20261116'], [100]], [['20261118'], [42]], [['20261110'], [121]]],
            $isClickReport => [[[], [23]]],
            default => [[[], [562]]],
        };
    });

    $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSeeText('Google Analytics sin conectar')
        ->assertSeeText('Meta 500/mes · 112 %')
        ->assertSeeText('Meta 50 al día 90 · 46 %')
        ->assertSeeTextInOrder(['LinkedIn', '182 · 43 %', 'Google', '141 · 34 %', 'Directo', '96 · 23 %'])
        ->assertSeeText('263 visitas en 4 semanas · de 0 a 142 por semana')
        ->assertSeeTextInOrder(['/mi-historia', '61'])
        ->assertSeeTextInOrder(['Descargas del CV', '18', '12 en español · 6 en inglés', 'Venezuela 12 · Spain 5 · País desconocido 1'])
        ->assertSeeTextInOrder([$article->title, 'Experiencia técnica', '58', '4 min 10 s', '5']);

    Http::assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer ga-token')
        && str_contains($request->url(), 'properties/123456:runReport'));
});

it('filters Google Analytics by the site language', function () {
    fakeGoogleAnalytics(fn (array $report): array => []);
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::dashboard')->set('locale', 'en');

    Http::assertSent(fn (Request $request): bool => str_contains($request->url(), 'runReport')
        && str_contains(json_encode($request->data()), '"fieldName":"customEvent:site_locale","stringFilter":{"value":"en"}'));
});

it('keeps working when Google Analytics fails', function () {
    fakeGoogleAnalytics(fn (array $report) => Http::response(['error' => 'quota'], 429));

    $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Google Analytics no respondió');
});

it('shows a finished plan and lets the period cover any plan', function () {
    $this->plan->update(['starts_on' => '2026-07-01']);
    $earlierPlan = GrowthPlan::factory()->create(['name' => 'Plan 0', 'starts_on' => '2026-03-01']);
    ContactMessage::factory()->create(['created_at' => '2026-04-10 12:00:00']);

    $this->actingAs(User::factory()->create(['locale' => 'es']));

    Livewire::test('pages::dashboard')
        ->assertSeeText('Plan 1 · terminó el 28 sep. 2026')
        ->assertDontSeeText('La línea marca dónde deberías ir hoy')
        ->set('period', 'plan-'.$earlierPlan->id)
        ->assertSet('period', 'plan-'.$earlierPlan->id)
        ->assertSeeText('Metas del plan que se registran a mano · Plan 0')
        ->assertSeeText('0 de 1 respondidos en menos de 48 h');
});

it('invites to create a plan when there is none', function () {
    GrowthPlan::query()->delete();

    $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Todavía no hay un plan.')
        ->assertSeeText('Crea un plan con sus metas para seguirlas aquí.')
        ->assertDontSeeText('Meta 500/mes');
});
