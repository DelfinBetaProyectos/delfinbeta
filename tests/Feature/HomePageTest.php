<?php

it('redirects the root to the home page in the browser language', function (string $acceptLanguage, string $expectedLocale) {
    $response = $this->get('/', ['Accept-Language' => $acceptLanguage]);

    $response->assertRedirect(route("{$expectedLocale}.home"));
})->with([
    'english browser' => ['en-US,en;q=0.9', 'en'],
    'spanish browser' => ['es-VE,es;q=0.9', 'es'],
    'unsupported language' => ['fr-FR,fr;q=0.9', 'es'],
    'empty language header' => ['', 'es'],
]);

it('renders the home page in the requested locale', function (string $locale, string $heading, string $alternateUrl) {
    $response = $this->get("/{$locale}");

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading)
        ->assertSee('href="'.url($alternateUrl).'"', false);
})->with([
    'spanish' => ['es', 'Plataformas reales, con nombre y resultado', '/en'],
    'english' => ['en', 'Real platforms, with names and results', '/es'],
]);

it('returns 404 for an unsupported locale prefix', function () {
    $this->get('/fr')->assertNotFound();
});

it('hides the writing section and renumbers the next ones when there are fewer than three articles', function () {
    app('translator')->addLines(['posts.items' => [
        'unico' => ['pillar' => 'tech', 'date' => '2026-11-10', 'title' => 'Único artículo', 'summary' => 'Resumen.'],
    ]], 'es');

    $response = $this->get('/es');

    $response->assertOk()
        ->assertDontSeeText('Últimos artículos')
        ->assertSeeText('04 · ¿Tienes un proyecto?')
        ->assertSeeText('05 · Contacto');
});

it('shows the writing section with three or more articles', function () {
    $this->get('/es')->assertOk()
        ->assertSeeTextInOrder(['01 · Mi historia', '02 · Proyectos seleccionados', '03 · Comunidad y charlas', '04 · Contenido', '05 · ¿Tienes un proyecto?', '06 · Contacto']);
});

it('shares the page with open graph tags and the brand image', function () {
    $response = $this->get('/es');

    $response->assertOk()
        ->assertSee('<meta property="og:title" content="'.e(__('home.meta_title', [], 'es').' - '.config('app.name')).'" />', false)
        ->assertSee('<meta property="og:url" content="'.url('/es').'" />', false)
        ->assertSee('<meta property="og:image" content="'.asset('images/brand/DelfinBeta_OG.png').'" />', false)
        ->assertSee('<meta property="og:locale" content="es_VE" />', false);
});
