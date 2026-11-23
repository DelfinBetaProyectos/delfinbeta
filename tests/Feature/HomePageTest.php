<?php

use App\Models\Article;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

it('renders the home page at the root in the browser language', function (string $acceptLanguage, string $locale, string $heading) {
    $response = $this->get('/', ['Accept-Language' => $acceptLanguage]);

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading);
})->with([
    'spanish browser' => ['es-VE,es;q=0.9', 'es', 'Plataformas reales, con nombre y resultado'],
    'english browser' => ['en-US,en;q=0.9', 'en', 'Real platforms, with names and results'],
    'unsupported language' => ['fr-FR,fr;q=0.9', 'es', 'Plataformas reales, con nombre y resultado'],
]);

it('no longer serves the locale-prefixed home pages', function (string $url) {
    $this->get($url)->assertNotFound();
})->with(['/es', '/en']);

it('hides the writing section and renumbers the next ones with fewer than three published articles', function () {
    Article::factory()->count(2)->create();
    Article::factory()->draft()->create();

    $response = $this->withSession(['locale' => 'es'])->get('/');

    $response->assertOk()
        ->assertDontSeeText('Últimos artículos')
        ->assertSeeTextInOrder(['01 · Mi historia', '02 · Proyectos seleccionados', '03 · Comunidad y charlas', '04 · ¿Tienes un proyecto?', '05 · Contacto']);
});

it('shows the three latest published articles in the writing section', function () {
    Article::factory()->create(['title' => ['es' => 'El más viejo', 'en' => 'Oldest'], 'published_at' => now()->subMonth()]);
    $latest = Article::factory()->create(['title' => ['es' => 'El más nuevo', 'en' => 'Newest'], 'published_at' => now()->subHour()]);
    Article::factory()->count(2)->create(['published_at' => now()->subWeek()]);
    Article::factory()->scheduled()->create(['title' => ['es' => 'Programado', 'en' => 'Scheduled']]);

    $this->withSession(['locale' => 'es'])->get('/')->assertOk()
        ->assertSeeTextInOrder(['01 · Mi historia', '02 · Proyectos seleccionados', '03 · Comunidad y charlas', '04 · Contenido', '05 · ¿Tienes un proyecto?', '06 · Contacto'])
        ->assertSeeText('El más nuevo')
        ->assertSee('href="'.route('blog.show', $latest).'"', false)
        ->assertDontSeeText('El más viejo')
        ->assertDontSeeText('Programado');
});

it('shows the blog link in the menu only once an article is published', function () {
    Article::factory()->draft()->create();
    Article::factory()->scheduled()->create();

    $this->get('/')->assertOk()->assertDontSee('href="'.route('blog.index').'"', false);

    Article::factory()->create();

    $this->get('/')->assertOk()->assertSee('href="'.route('blog.index').'"', false);
});

it('shares the page with open graph tags and the brand image', function () {
    $response = $this->withSession(['locale' => 'es'])->get('/');

    $response->assertOk()
        ->assertSee('<meta property="og:title" content="'.e(__('home.meta_title', [], 'es').' - '.config('app.name')).'" />', false)
        ->assertSee('<meta property="og:url" content="'.url('/').'" />', false)
        ->assertSee('<meta property="og:image" content="'.asset('images/brand/DelfinBeta_OG.png').'" />', false)
        ->assertSee('<meta property="og:locale" content="es_VE" />', false);
});

it('shows the years of experience in the story heading', function () {
    config(['site.career_started_at' => '2008-01-01']);
    $this->travelTo('2026-11-20');

    $this->withSession(['locale' => 'es'])->get('/')->assertOk()
        ->assertSeeText('18 años construyendo software y fundando empresas');
});

it('shows the screenshot on each project card that has one', function () {
    Project::factory()->create(['image' => 'projects/KOPAPLAY.jpg']);

    $this->withSession(['locale' => 'es'])->get('/')->assertOk()
        ->assertSee('src="'.Storage::disk('public')->url('projects/KOPAPLAY.jpg').'"', false);
});

it('lists the DEV profile with the other social networks in the contact section', function () {
    $this->get('/')->assertOk()
        ->assertSee('href="https://dev.to/delfinbeta"', false)
        ->assertSeeTextInOrder(['LinkedIn', 'GitHub', 'Medium', 'DEV', 'Instagram']);
});
