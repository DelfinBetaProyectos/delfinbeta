<?php

it('renders a project case study at its translated url', function (string $url, string $locale, string $heading, string $alternateUrl) {
    $response = $this->get($url);

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading)
        ->assertSee('href="'.url($alternateUrl).'"', false);
})->with([
    'spanish' => ['/es/proyectos/felaban', 'es', 'Una plataforma de registro para todos los congresos de FELABAN', '/en/projects/felaban'],
    'english' => ['/en/projects/felaban', 'en', 'One registration platform for every FELABAN conference', '/es/proyectos/felaban'],
]);

it('returns 404 for an unknown project', function () {
    $this->get('/es/proyectos/no-existe')->assertNotFound();
});

it('links to the previous and next projects, wrapping around the list', function (string $url, string $previousUrl, string $nextUrl) {
    $response = $this->get($url);

    $response->assertSee('href="'.url($previousUrl).'" rel="prev"', false)
        ->assertSee('href="'.url($nextUrl).'" rel="next"', false);
})->with([
    'first project' => ['/es/proyectos/felaban', '/es/proyectos/ci-cd', '/es/proyectos/kopaplay'],
    'last project' => ['/es/proyectos/ci-cd', '/es/proyectos/squaad', '/es/proyectos/felaban'],
]);

it('hides the live link when the project has no public url', function () {
    $this->get('/es/proyectos/kopaplay')->assertOk()->assertDontSeeText('En vivo');
});

it('links each home project card to its case study', function () {
    $this->get('/es')->assertSee('href="'.url('/es/proyectos/felaban').'"', false);
});
