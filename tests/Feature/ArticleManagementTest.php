<?php

use App\Enums\ArticleCategory;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/**
 * Fill every required translatable field of the article form in both locales.
 */
function fillRequiredArticleFields(mixed $component): mixed
{
    foreach (['es', 'en'] as $locale) {
        $component
            ->set("form.title.{$locale}", "Título {$locale}")
            ->set("form.summary.{$locale}", "Resumen {$locale}")
            ->set("form.body.{$locale}", "## Cuerpo {$locale}");
    }

    return $component;
}

beforeEach(function () {
    $this->actingAs(User::factory()->create(['locale' => 'es']));
    app()->setLocale('es');
});

it('keeps the articles dashboard behind the login', function (string $routeName) {
    auth()->logout();

    $this->get(route($routeName))->assertRedirect(route('login'));
})->with(['dashboard.articles.index', 'dashboard.articles.create']);

it('lists drafts, scheduled and published articles and explains when the blog shows', function () {
    Article::factory()->draft()->create(['slug' => 'un-borrador', 'title' => ['es' => 'Un borrador', 'en' => 'A draft']]);
    Article::factory()->scheduled()->create(['title' => ['es' => 'Uno programado', 'en' => 'Scheduled']]);
    Article::factory()->create(['title' => ['es' => 'Uno publicado', 'en' => 'Published']]);

    $this->get(route('dashboard.articles.index'))
        ->assertOk()
        ->assertSeeTextInOrder(['Publicado', 'Un borrador', 'un-borrador', 'Borrador', 'Uno programado', 'Programado', 'Uno publicado'])
        ->assertSeeText('1 artículo publicado')
        ->assertSeeText('El blog se ve en el menú.')
        ->assertSeeText('El inicio muestra los últimos artículos cuando haya 3 publicados.');
});

it('creates a draft with a slug suggested from the Spanish title', function () {
    $component = Livewire::test('pages::articles.form')
        ->set('form.title.es', 'Pagos recurrentes en un SaaS')
        ->assertSet('form.slug', 'pagos-recurrentes-en-un-saas')
        ->set('form.category', ArticleCategory::Business->value);

    fillRequiredArticleFields($component)
        ->set('form.title.es', 'Pagos recurrentes en un SaaS')
        ->call('save')
        ->assertHasNoErrors();

    $article = Article::sole();

    expect($article)
        ->slug->toBe('pagos-recurrentes-en-un-saas')
        ->category->toBe(ArticleCategory::Business)
        ->published_at->toBeNull()
        ->and($article->getTranslations('body'))->toBe(['es' => '## Cuerpo es', 'en' => '## Cuerpo en']);
});

it('publishes on the chosen day in Maracay time', function () {
    $this->travelTo(now()->setTimezone('America/Caracas')->setTime(10, 0));

    $component = Livewire::test('pages::articles.form')->set('form.slug', 'hoy');
    fillRequiredArticleFields($component)->set('form.published_on', now('America/Caracas')->toDateString())->call('save')->assertHasNoErrors();

    $article = Article::sole();

    expect($article->isPublished())->toBeTrue()
        ->and($article->published_at->timezone('America/Caracas')->format('Y-m-d H:i'))->toBe(now('America/Caracas')->format('Y-m-d').' 00:00');
});

it('schedules an article for a future date', function () {
    $component = Livewire::test('pages::articles.form')->set('form.slug', 'futuro');
    fillRequiredArticleFields($component)
        ->set('form.published_on', now('America/Caracas')->addWeek()->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    expect(Article::sole())->isPublished()->toBeFalse()->isScheduled()->toBeTrue();
});

it('edits an article and can send it back to draft', function () {
    $article = Article::factory()->create(['slug' => 'viejo']);

    Livewire::test('pages::articles.form', ['article' => $article])
        ->assertSet('form.published_on', $article->published_at->timezone('America/Caracas')->toDateString())
        ->set('form.slug', 'nuevo')
        ->set('form.published_on', '')
        ->set('form.title.en', 'New English title')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard.articles.edit', 'nuevo'));

    expect($article->refresh())
        ->slug->toBe('nuevo')
        ->published_at->toBeNull()
        ->and($article->getTranslation('title', 'en'))->toBe('New English title');
});

it('keeps a single featured article', function () {
    $previouslyFeatured = Article::factory()->featured()->create();

    $component = Livewire::test('pages::articles.form')->set('form.slug', 'nuevo-destacado')->set('form.is_featured', true);
    fillRequiredArticleFields($component)->call('save')->assertHasNoErrors();

    expect($previouslyFeatured->refresh()->is_featured)->toBeFalse()
        ->and(Article::query()->where('slug', 'nuevo-destacado')->value('is_featured'))->toBeTrue();
});

it('validates the article form', function (string $field, mixed $value, string $error) {
    $component = Livewire::test('pages::articles.form')->set('form.slug', 'valido');
    fillRequiredArticleFields($component)
        ->set($field, $value)
        ->call('save')
        ->assertHasErrors($error);

    expect(Article::count())->toBe(0);
})->with([
    'missing english title' => ['form.title.en', '', 'form.title.en'],
    'missing spanish body' => ['form.body.es', '', 'form.body.es'],
    'invalid slug' => ['form.slug', 'Con Espacios', 'form.slug'],
    'unknown category' => ['form.category', 'prices', 'form.category'],
    'invalid date' => ['form.published_on', '22/11/2026', 'form.published_on'],
    'invalid medium url' => ['form.medium_url', 'no-es-url', 'form.medium_url'],
    'invalid dev url' => ['form.dev_url', 'no-es-url', 'form.dev_url'],
]);

it('rejects a slug that another article uses', function () {
    Article::factory()->create(['slug' => 'repetido']);

    $component = Livewire::test('pages::articles.form')->set('form.slug', 'repetido');
    fillRequiredArticleFields($component)->call('save')->assertHasErrors('form.slug');
});

it('deletes an article after confirming', function () {
    $article = Article::factory()->create(['title' => ['es' => 'Para borrar', 'en' => 'To delete']]);

    Livewire::test('pages::articles.index')
        ->call('confirmDelete', $article->id)
        ->call('delete')
        ->assertDontSee('Para borrar');

    expect(Article::count())->toBe(0);
});

it('stores the cover image and replaces the previous file', function () {
    Storage::fake('public');

    $component = Livewire::test('pages::articles.form')->set('form.slug', 'con-portada');
    fillRequiredArticleFields($component)
        ->set('form.image', UploadedFile::fake()->image('portada.jpg', 1600, 900))
        ->call('save')
        ->assertHasNoErrors();

    $firstCover = Article::sole()->image;
    Storage::disk('public')->assertExists($firstCover);
    expect($firstCover)->toStartWith('articles/');

    Livewire::test('pages::articles.form', ['article' => Article::sole()])
        ->set('form.image', UploadedFile::fake()->image('nueva.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing($firstCover);
    Storage::disk('public')->assertExists(Article::sole()->image);
});

it('removes the cover image when asked', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('portada.jpg')->store('articles', 'public');
    $article = Article::factory()->create(['image' => $path]);

    Livewire::test('pages::articles.form', ['article' => $article])
        ->set('form.remove_image', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($article->refresh()->image)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('rejects a cover that is not an image', function () {
    Storage::fake('public');

    $component = Livewire::test('pages::articles.form')->set('form.slug', 'sin-imagen');
    fillRequiredArticleFields($component)
        ->set('form.image', UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertHasErrors('form.image');
});

it('deletes the cover image file with the article', function () {
    Storage::fake('public');
    $path = UploadedFile::fake()->image('portada.jpg')->store('articles', 'public');
    $article = Article::factory()->create(['image' => $path]);

    Livewire::test('pages::articles.index')->call('confirmDelete', $article->id)->call('delete');

    Storage::disk('public')->assertMissing($path);
});

it('shows a publish switch for each article in the list', function () {
    $published = Article::factory()->create();
    $draft = Article::factory()->draft()->create();

    $html = Livewire::test('pages::articles.index')->html();

    expect($html)
        ->toMatch('/<ui-switch[^>]*wire:change="togglePublished\('.$published->id.'\)"[^>]*checked data-checked/')
        ->toMatch('/<ui-switch[^>]*wire:change="togglePublished\('.$draft->id.'\)"(?![^>]*data-checked)[^>]*>/');
});

it('publishes a draft today from the switch', function () {
    $this->travelTo(now('America/Caracas')->setTime(15, 30));
    $article = Article::factory()->draft()->create();

    Livewire::test('pages::articles.index')->call('togglePublished', $article->id);

    $article->refresh();
    expect($article->isPublished())->toBeTrue()
        ->and($article->published_at->timezone('America/Caracas')->format('Y-m-d H:i'))->toBe(now('America/Caracas')->format('Y-m-d').' 00:00');
});

it('sends a published article back to draft from the switch', function () {
    $article = Article::factory()->create();

    Livewire::test('pages::articles.index')->call('togglePublished', $article->id);

    expect($article->refresh()->published_at)->toBeNull();
});

it('publishes a scheduled article today from the switch', function () {
    $article = Article::factory()->scheduled()->create();

    Livewire::test('pages::articles.index')->call('togglePublished', $article->id);

    expect($article->refresh())
        ->isPublished()->toBeTrue()
        ->published_at->isToday()->toBeTrue();
});
