<?php

it('renders the story page in the visitor language', function (string $locale, string $heading) {
    $response = $this->withSession(['locale' => $locale])->get('/mi-historia');

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading);
})->with([
    'spanish' => ['es', 'Cada etapa dejó algo que hoy uso'],
    'english' => ['en', 'Every stage left something I still use'],
]);

it('no longer serves the locale-prefixed urls', function (string $url) {
    $this->get($url)->assertNotFound();
})->with([
    '/es/mi-historia',
    '/en/my-story',
]);

it('marks the story link as the current page in the navigation', function () {
    $response = $this->get('/mi-historia');

    expect($response->getContent())->toMatch('#href="'.preg_quote(url('/mi-historia'), '#').'"\s+aria-current="page"#');
});

it('links the projects call to action to the projects page', function () {
    $this->get('/mi-historia')
        ->assertOk()
        ->assertSee('href="'.route('projects.index').'"', false)
        ->assertDontSee(route('home').'#'.__('nav.sections.projects.anchor'), false);
});
