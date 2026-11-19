<?php

it('renders the blog in each locale', function (string $url, string $locale, string $heading, string $alternateUrl) {
    $response = $this->get($url);

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading)
        ->assertSee('href="'.url($alternateUrl).'"', false);
})->with([
    'spanish' => ['/es/blog', 'es', 'Lo que aprendo construyendo, contado en simple', '/en/blog'],
    'english' => ['/en/blog', 'en', 'What I learn while building, explained simply', '/es/blog'],
]);

it('lists every article apart from the featured one when no filter is applied', function () {
    $response = $this->get('/es/blog');

    $response->assertOk()
        ->assertSeeText('Git, GitHub y GitLab: la guía, actualizada')
        ->assertSeeText('8 artículos')
        ->assertSee('href="'.url('/es/blog').'" aria-current="page"', false);
});

it('shows only the articles of the selected pillar', function () {
    $response = $this->get('/es/blog?pillar=mama');

    $response->assertOk()
        ->assertSeeText('Programar con un niño pequeño en casa')
        ->assertDontSeeText('Cofundar una agencia con socios')
        ->assertDontSeeText('Git, GitHub y GitLab: la guía, actualizada')
        ->assertSeeText('2 artículos');

    expect($response->getContent())->toMatch('#href="'.preg_quote(url('/es/blog').'?pillar=mama', '#').'"\s+aria-current="page"#');
});

it('keeps the featured article when it belongs to the selected pillar', function () {
    $this->get('/es/blog?pillar=tech')
        ->assertOk()
        ->assertSeeText('Git, GitHub y GitLab: la guía, actualizada')
        ->assertSeeText('4 artículos');
});

it('ignores an unknown pillar and shows every article', function () {
    $this->get('/es/blog?pillar=nope')->assertOk()->assertSeeText('8 artículos');
});

it('marks the blog link as the current page in the navigation', function () {
    $content = $this->get('/es/blog?pillar=mama')->getContent();

    $currentBlogLinks = preg_match_all('#href="'.preg_quote(url('/es/blog'), '#').'"\s+aria-current="page"[^>]*>Blog</a>#', $content);

    expect($currentBlogLinks)->toBe(2);
});

it('keeps the active filter when switching language', function () {
    $this->get('/es/blog?pillar=mama')->assertSee('href="'.url('/en/blog').'?pillar=mama"', false);
});
