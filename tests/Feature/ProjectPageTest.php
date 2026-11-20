<?php

beforeEach(function () {
    $this->withSession(['locale' => 'es']);
});

it('renders a project case study in the visitor language', function (string $locale, string $heading) {
    $response = $this->withSession(['locale' => $locale])->get('/proyectos/felaban');

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading);
})->with([
    'spanish' => ['es', 'Una plataforma de registro para todos los congresos de FELABAN'],
    'english' => ['en', 'One registration platform for every FELABAN conference'],
]);

it('returns 404 for an unknown project', function () {
    $this->get('/proyectos/no-existe')->assertNotFound();
});

it('links to the previous and next projects, wrapping around the list', function (string $url, string $previousUrl, string $nextUrl) {
    $response = $this->get($url);

    $response->assertSee('href="'.url($previousUrl).'" rel="prev"', false)
        ->assertSee('href="'.url($nextUrl).'" rel="next"', false);
})->with([
    'first project' => ['/proyectos/felaban', '/proyectos/ci-cd', '/proyectos/kopaplay'],
    'last project' => ['/proyectos/ci-cd', '/proyectos/squaad', '/proyectos/felaban'],
]);

it('hides the live link when the project has no public url', function () {
    $this->get('/proyectos/kopaplay')->assertOk()->assertDontSeeText('En vivo');
});

it('links each home project card to its case study', function () {
    $this->get('/')->assertSee('href="'.url('/proyectos/felaban').'"', false);
});
