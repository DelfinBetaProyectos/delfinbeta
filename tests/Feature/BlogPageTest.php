<?php

use App\Enums\ArticleCategory;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withSession(['locale' => 'es']);
});

it('does not exist until an article is published', function () {
    Article::factory()->draft()->create();
    Article::factory()->scheduled()->create();

    $this->get('/blog')->assertNotFound();
});

it('renders the blog in the visitor language', function (string $locale, string $heading, string $title) {
    Article::factory()->create(['title' => ['es' => 'Título en español', 'en' => 'English title']]);

    $this->withSession(['locale' => $locale])->get('/blog')
        ->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading)
        ->assertSeeText($title);
})->with([
    'spanish' => ['es', 'Lo que aprendo construyendo, contado en simple', 'Título en español'],
    'english' => ['en', 'What I learn while building, explained simply', 'English title'],
]);

it('lists only published articles, newest first, with the featured one on top', function () {
    $featured = Article::factory()->featured()->create(['title' => ['es' => 'Artículo destacado', 'en' => 'Featured'], 'image' => 'articles/portada.jpg']);
    Article::factory()->create(['title' => ['es' => 'Artículo viejo', 'en' => 'Old'], 'published_at' => now()->subMonth()]);
    Article::factory()->create(['title' => ['es' => 'Artículo nuevo', 'en' => 'New'], 'published_at' => now()->subHour()]);
    Article::factory()->draft()->create(['title' => ['es' => 'Borrador', 'en' => 'Draft']]);
    Article::factory()->scheduled()->create(['title' => ['es' => 'Programado', 'en' => 'Scheduled']]);

    $response = $this->get('/blog');

    $response->assertOk()
        ->assertSeeTextInOrder(['Destacado', 'Artículo destacado', '2 artículos', 'Artículo nuevo', 'Artículo viejo'])
        ->assertSee('src="'.Storage::disk('public')->url('articles/portada.jpg').'"', false)
        ->assertSee('href="'.route('blog.show', $featured).'"', false)
        ->assertDontSeeText('Borrador')
        ->assertDontSeeText('Programado');

    expect($response->getContent())->toMatch('#href="'.preg_quote(url('/blog'), '#').'"\s+aria-current="page"#');
});

it('shows only the articles of the selected category', function () {
    Article::factory()->category(ArticleCategory::Mama)->create(['title' => ['es' => 'Programar con un niño', 'en' => 'Coding with a kid']]);
    Article::factory()->category(ArticleCategory::Business)->create(['title' => ['es' => 'Cofundar una agencia', 'en' => 'Co-founding']]);

    $response = $this->get('/blog?category=mama');

    $response->assertOk()
        ->assertSeeText('Programar con un niño')
        ->assertDontSeeText('Cofundar una agencia')
        ->assertSeeText('1 artículo');

    expect($response->getContent())->toMatch('#href="'.preg_quote(url('/blog').'?category=mama', '#').'"\s+aria-current="page"#');
});

it('ignores an unknown category and shows every article', function () {
    Article::factory()->count(2)->create();

    $this->get('/blog?category=nope')->assertOk()->assertSeeText('2 artículos');
});

it('marks the blog link as the current page in the navigation', function () {
    Article::factory()->create();

    $content = $this->get('/blog?category=mama')->getContent();

    $currentBlogLinks = preg_match_all('#href="'.preg_quote(url('/blog'), '#').'"\s+aria-current="page"[^>]*>Blog</a>#', $content);

    expect($currentBlogLinks)->toBe(2);
});

it('shows a published article with its Markdown body, reading time and related articles', function () {
    $article = Article::factory()->category(ArticleCategory::Tech)->create([
        'title' => ['es' => 'CI/CD con GitHub Actions', 'en' => 'CI/CD with GitHub Actions'],
        'body' => ['es' => "## Primer paso\n\nUsa **GitHub Actions** con [la guía](https://docs.github.com).", 'en' => 'Body'],
        'medium_url' => 'https://medium.com/@delfinbeta/ci-cd',
        'dev_url' => 'https://dev.to/delfinbeta/ci-cd',
    ]);
    Article::factory()->category(ArticleCategory::Tech)->create(['title' => ['es' => 'Otro técnico', 'en' => 'Other tech']]);
    Article::factory()->category(ArticleCategory::Mama)->create(['title' => ['es' => 'De otro tema', 'en' => 'Other topic']]);

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertSee('<meta property="og:title" content="'.e('CI/CD con GitHub Actions - '.config('app.name')).'" />', false)
        ->assertSee('<h2>Primer paso</h2>', false)
        ->assertSee('<strong>GitHub Actions</strong>', false)
        ->assertSee('href="https://medium.com/@delfinbeta/ci-cd"', false)
        ->assertSee('href="https://dev.to/delfinbeta/ci-cd"', false)
        ->assertSeeText('Este artículo también está publicado en Medium y DEV.')
        ->assertSeeText('1 min')
        ->assertSeeText('Más sobre Experiencia técnica')
        ->assertSeeText('Otro técnico')
        ->assertDontSeeText('De otro tema');
});

it('escapes raw HTML written in the article body', function () {
    $article = Article::factory()->create(['body' => ['es' => "Hola <script>alert('x')</script>", 'en' => 'Hi']]);

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertDontSee("<script>alert('x')</script>", false)
        ->assertSee('&lt;script&gt;', false);
});

it('does not show drafts or scheduled articles', function (string $state) {
    $article = Article::factory()->{$state}()->create();

    $this->get(route('blog.show', $article))->assertNotFound();
})->with(['draft', 'scheduled']);

it('keeps community articles apart from #MamaDeveloper', function () {
    Article::factory()->category(ArticleCategory::Community)->create(['title' => ['es' => 'Startup Weekend Maracay', 'en' => 'Startup Weekend Maracay']]);
    Article::factory()->category(ArticleCategory::Mama)->create(['title' => ['es' => 'Programar con un niño', 'en' => 'Coding with a kid']]);

    $this->get('/blog')->assertOk()->assertSeeTextInOrder(['#MamaDeveloper', 'Comunidad y charlas']);

    $this->get('/blog?category=community')
        ->assertOk()
        ->assertSeeText('Startup Weekend Maracay')
        ->assertDontSeeText('Programar con un niño');
});

it('shows the cover image at the top of the article', function () {
    $article = Article::factory()->create(['image' => 'articles/portada.jpg']);
    $withoutCover = Article::factory()->create();

    $this->get(route('blog.show', $article))->assertOk()
        ->assertSee('src="'.Storage::disk('public')->url('articles/portada.jpg').'"', false);

    $this->get(route('blog.show', $withoutCover))->assertOk()
        ->assertDontSee('/storage/articles/', false);
});

it('shares an article with its cover image, or the brand image when it has none', function () {
    $withCover = Article::factory()->create(['title' => ['es' => 'Con portada', 'en' => 'With cover'], 'image' => 'articles/portada.jpg']);
    $withoutCover = Article::factory()->create();
    $coverUrl = Storage::disk('public')->url('articles/portada.jpg');

    $this->get(route('blog.show', $withCover))->assertOk()
        ->assertSee('<meta property="og:image" content="'.$coverUrl.'" />', false)
        ->assertSee('<meta name="twitter:image" content="'.$coverUrl.'" />', false)
        ->assertSee('<meta property="og:image:alt" content="'.e('Con portada - '.config('app.name')).'" />', false)
        ->assertDontSee('og:image:width', false);

    $this->get(route('blog.show', $withoutCover))->assertOk()
        ->assertSee('<meta property="og:image" content="'.asset('images/brand/DelfinBeta_OG.png').'" />', false)
        ->assertSee('<meta property="og:image:width" content="1200" />', false);
});

it('mentions only the platforms an article is also published on', function () {
    $onlyDev = Article::factory()->create(['dev_url' => 'https://dev.to/delfinbeta/solo-dev']);
    $nowhereElse = Article::factory()->create();

    $this->get(route('blog.show', $onlyDev))->assertOk()
        ->assertSeeText('Este artículo también está publicado en DEV.')
        ->assertDontSee('medium.com/@delfinbeta/', false);

    $this->get(route('blog.show', $nowhereElse))->assertOk()
        ->assertDontSeeText('también está publicado en');
});

it('links to the Medium and DEV profiles in the blog introduction', function () {
    Article::factory()->create();

    $this->get('/blog')->assertOk()
        ->assertSeeText('Cada artículo también se publica en Medium y DEV.')
        ->assertSee('href="'.config('site.social.DEV').'"', false);
});
