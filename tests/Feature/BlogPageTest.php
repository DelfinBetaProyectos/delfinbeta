<?php

beforeEach(function () {
    $this->withSession(['locale' => 'es']);
});

it('renders the blog in the visitor language', function (string $locale, string $heading) {
    $response = $this->withSession(['locale' => $locale])->get('/blog');

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading);
})->with([
    'spanish' => ['es', 'Lo que aprendo construyendo, contado en simple'],
    'english' => ['en', 'What I learn while building, explained simply'],
]);

it('lists every article apart from the featured one when no filter is applied', function () {
    $response = $this->get('/blog');

    $response->assertOk()
        ->assertSeeText('Git, GitHub y GitLab: la guía, actualizada')
        ->assertSeeText('8 artículos');

    expect($response->getContent())->toMatch('#href="'.preg_quote(url('/blog'), '#').'"\s+aria-current="page"#');
});

it('shows only the articles of the selected pillar', function () {
    $response = $this->get('/blog?pillar=mama');

    $response->assertOk()
        ->assertSeeText('Programar con un niño pequeño en casa')
        ->assertDontSeeText('Cofundar una agencia con socios')
        ->assertDontSeeText('Git, GitHub y GitLab: la guía, actualizada')
        ->assertSeeText('2 artículos');

    expect($response->getContent())->toMatch('#href="'.preg_quote(url('/blog').'?pillar=mama', '#').'"\s+aria-current="page"#');
});

it('keeps the featured article when it belongs to the selected pillar', function () {
    $this->get('/blog?pillar=tech')
        ->assertOk()
        ->assertSeeText('Git, GitHub y GitLab: la guía, actualizada')
        ->assertSeeText('4 artículos');
});

it('ignores an unknown pillar and shows every article', function () {
    $this->get('/blog?pillar=nope')->assertOk()->assertSeeText('8 artículos');
});

it('marks the blog link as the current page in the navigation', function () {
    $content = $this->get('/blog?pillar=mama')->getContent();

    $currentBlogLinks = preg_match_all('#href="'.preg_quote(url('/blog'), '#').'"\s+aria-current="page"[^>]*>Blog</a>#', $content);

    expect($currentBlogLinks)->toBe(2);
});
