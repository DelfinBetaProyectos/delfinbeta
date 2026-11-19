<?php

it('renders the story page at its translated slug', function (string $url, string $locale, string $heading, string $alternateUrl) {
    $response = $this->get($url);

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading)
        ->assertSee('href="'.url($alternateUrl).'"', false);
})->with([
    'spanish' => ['/es/mi-historia', 'es', 'Cada etapa dejó algo que hoy uso', '/en/my-story'],
    'english' => ['/en/my-story', 'en', 'Every stage left something I still use', '/es/mi-historia'],
]);

it('returns 404 when a slug is used with the other locale prefix', function (string $url) {
    $this->get($url)->assertNotFound();
})->with([
    '/es/my-story',
    '/en/mi-historia',
]);

it('marks the story link as the current page in the navigation', function () {
    $response = $this->get('/es/mi-historia');

    $response->assertSee('href="'.url('/es/mi-historia').'" aria-current="page"', false);
});
