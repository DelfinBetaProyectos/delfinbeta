<?php

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

it('hides the writing section and renumbers the next ones when there are fewer than three articles', function () {
    app('translator')->addLines(['posts.items' => [
        'unico' => ['pillar' => 'tech', 'date' => '2026-11-10', 'title' => 'Único artículo', 'summary' => 'Resumen.'],
    ]], 'es');

    $response = $this->withSession(['locale' => 'es'])->get('/');

    $response->assertOk()
        ->assertDontSeeText('Últimos artículos')
        ->assertSeeText('04 · ¿Tienes un proyecto?')
        ->assertSeeText('05 · Contacto');
});

it('shows the writing section with three or more articles', function () {
    $this->withSession(['locale' => 'es'])->get('/')->assertOk()
        ->assertSeeTextInOrder(['01 · Mi historia', '02 · Proyectos seleccionados', '03 · Comunidad y charlas', '04 · Contenido', '05 · ¿Tienes un proyecto?', '06 · Contacto']);
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
