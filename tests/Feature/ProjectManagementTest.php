<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

/**
 * Fill every required translatable field of the project form in both locales.
 */
function fillRequiredProjectFields(mixed $component): mixed
{
    foreach (['es', 'en'] as $locale) {
        $component
            ->set("form.title.{$locale}", "Título {$locale}")
            ->set("form.summary.{$locale}", "Resumen {$locale}")
            ->set("form.context.{$locale}", "Contexto {$locale}")
            ->set("form.problem.{$locale}", "Problema {$locale}")
            ->set("form.result.{$locale}", "Resultado {$locale}");
    }

    return $component;
}

it('redirects guests to the login page', function (string $routeName) {
    $project = Project::factory()->create();

    $this->get(route($routeName, $project))->assertRedirect(route('login'));
})->with([
    'list' => ['dashboard.projects.index'],
    'create' => ['dashboard.projects.create'],
    'edit' => ['dashboard.projects.edit'],
]);

it('lists every project, published or not', function () {
    Project::factory()->create(['title' => ['es' => 'Publicado', 'en' => 'Published one']]);
    Project::factory()->unpublished()->create(['title' => ['es' => 'Borrador', 'en' => 'Draft one']]);

    $this->actingAs(User::factory()->create(['locale' => 'es']))
        ->get(route('dashboard.projects.index'))
        ->assertOk()
        ->assertSeeText('Publicado')
        ->assertSeeText('Borrador');
});

it('creates a project with both languages, lists and a screenshot', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());

    $component = fillRequiredProjectFields(Livewire::test('pages::projects.form'))
        ->set('form.slug', 'nuevo-proyecto')
        ->set('form.published_on', now('America/Caracas')->toDateString())
        ->set('form.built.es', "Pagos\n\nReportes ")
        ->set('form.stats.es', "+12 | ligas\n2022 | en uso desde")
        ->set('form.image', UploadedFile::fake()->image('captura.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $project = Project::query()->sole();

    $component->assertRedirect(route('dashboard.projects.edit', $project));

    expect($project->slug)->toBe('nuevo-proyecto')
        ->and($project->isPublished())->toBeTrue()
        ->and($project->getTranslations('title'))->toBe(['es' => 'Título es', 'en' => 'Título en'])
        ->and($project->label)->toBeNull()
        ->and($project->getTranslations('built'))->toBe(['es' => ['Pagos', 'Reportes'], 'en' => []])
        ->and($project->getTranslations('stats')['es'])->toBe([
            ['value' => '+12', 'label' => 'ligas'],
            ['value' => '2022', 'label' => 'en uso desde'],
        ]);

    Storage::disk('public')->assertExists($project->image);
});

it('suggests the slug from the spanish title when creating', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.form')
        ->set('form.title.es', 'Registro a congresos')
        ->assertSet('form.slug', 'registro-a-congresos');
});

it('requires the main texts in every language', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.form')
        ->set('form.slug', 'nuevo')
        ->call('save')
        ->assertHasErrors([
            'form.title.es' => 'required', 'form.title.en' => 'required',
            'form.summary.en' => 'required',
            'form.context.en' => 'required',
            'form.problem.en' => 'required', 'form.result.en' => 'required',
        ]);

    expect(Project::query()->count())->toBe(0);
});

it('rejects an invalid or duplicated slug', function (string $slug) {
    Project::factory()->create(['slug' => 'existente']);
    $this->actingAs(User::factory()->create());

    fillRequiredProjectFields(Livewire::test('pages::projects.form'))
        ->set('form.slug', $slug)
        ->call('save')
        ->assertHasErrors('form.slug');
})->with([
    'duplicated' => ['existente'],
    'uppercase and spaces' => ['Mi Proyecto'],
]);

it('rejects figures without a label', function () {
    app()->setLocale('es');
    $this->actingAs(User::factory()->create());

    fillRequiredProjectFields(Livewire::test('pages::projects.form'))
        ->set('form.slug', 'nuevo')
        ->set('form.stats.en', '+12')
        ->call('save')
        ->assertHasErrors(['form.stats.en' => 'Cada cifra debe escribirse como "valor | etiqueta".']);
});

it('edits a project, keeping its slug, and replaces its screenshot', function () {
    Storage::fake('public');
    Storage::disk('public')->put('projects/vieja.jpg', 'old');
    $project = Project::factory()->create(['slug' => 'kopaplay', 'image' => 'projects/vieja.jpg']);
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.form', ['project' => $project])
        ->assertSet('form.slug', 'kopaplay')
        ->set('form.title.en', 'KOPAPLAY · Tournaments')
        ->set('form.image', UploadedFile::fake()->image('nueva.jpg'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $project->refresh();

    expect($project->getTranslation('title', 'en'))->toBe('KOPAPLAY · Tournaments')
        ->and($project->image)->not->toBe('projects/vieja.jpg');

    Storage::disk('public')->assertMissing('projects/vieja.jpg');
    Storage::disk('public')->assertExists($project->image);
});

it('removes the screenshot when asked', function () {
    Storage::fake('public');
    Storage::disk('public')->put('projects/captura.jpg', 'img');
    $project = Project::factory()->create(['image' => 'projects/captura.jpg']);
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.form', ['project' => $project])
        ->set('form.remove_image', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($project->refresh()->image)->toBeNull();
    Storage::disk('public')->assertMissing('projects/captura.jpg');
});

it('toggles whether a project is published', function () {
    $project = Project::factory()->unpublished()->create();
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.index')->call('togglePublished', $project->id);

    expect($project->refresh()->isPublished())->toBeTrue();
});

it('deletes a project and its screenshot after confirmation', function () {
    Storage::fake('public');
    Storage::disk('public')->put('projects/captura.jpg', 'img');
    $project = Project::factory()->create(['image' => 'projects/captura.jpg']);
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.index')
        ->call('confirmDelete', $project->id)
        ->call('delete');

    $this->assertModelMissing($project);
    Storage::disk('public')->assertMissing('projects/captura.jpg');
});

it('renders the create and edit pages', function () {
    $project = Project::factory()->create(['title' => ['es' => 'Mi proyecto', 'en' => 'My project']]);
    $this->actingAs(User::factory()->create(['locale' => 'es']));

    $this->get(route('dashboard.projects.create'))->assertOk()->assertSeeText('Nuevo proyecto');
    $this->get(route('dashboard.projects.edit', $project))->assertOk()->assertSeeText('Mi proyecto');
});

it('adds build screenshots and deletes the ones removed', function () {
    Storage::fake('public');
    Storage::disk('public')->put('projects/vieja.jpg', 'old');
    Storage::disk('public')->put('projects/sigue.jpg', 'keep');
    $project = Project::factory()->create(['built_images' => ['projects/vieja.jpg', 'projects/sigue.jpg']]);
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.form', ['project' => $project])
        ->call('removeBuiltImage', 0)
        ->set('form.new_built_images', [UploadedFile::fake()->image('nueva.jpg')])
        ->call('save')
        ->assertHasNoErrors();

    $builtImages = $project->refresh()->built_images;

    expect($builtImages)->toHaveCount(2)
        ->and($builtImages[0])->toBe('projects/sigue.jpg');

    Storage::disk('public')->assertMissing('projects/vieja.jpg');
    Storage::disk('public')->assertExists($builtImages[1]);
});

it('limits the number of build screenshots', function () {
    Storage::fake('public');
    $project = Project::factory()->create(['built_images' => array_map(fn (int $i): string => "projects/{$i}.jpg", range(1, 5))]);
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.form', ['project' => $project])
        ->set('form.new_built_images', [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')])
        ->call('save')
        ->assertHasErrors(['form.new_built_images' => 'max']);
});

it('shows a publish switch for each project in the list', function () {
    $published = Project::factory()->create();
    $hidden = Project::factory()->unpublished()->create();
    $this->actingAs(User::factory()->create());

    $html = Livewire::test('pages::projects.index')->html();

    expect($html)
        ->toMatch('/<ui-switch[^>]*wire:change="togglePublished\('.$published->id.'\)"[^>]*checked data-checked/')
        ->toMatch('/<ui-switch[^>]*wire:change="togglePublished\('.$hidden->id.'\)"(?![^>]*data-checked)[^>]*>/');
});

it('hides a published project from the switch', function () {
    $project = Project::factory()->create();
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.index')->call('togglePublished', $project->id);

    expect($project->refresh()->published_at)->toBeNull();
});

it('publishes a draft project today from the switch, in Maracay time', function () {
    $this->travelTo(now('America/Caracas')->setTime(15, 30));
    $project = Project::factory()->unpublished()->create();
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.index')->call('togglePublished', $project->id);

    expect($project->refresh()->published_at->timezone('America/Caracas')->format('Y-m-d H:i'))
        ->toBe(now('America/Caracas')->format('Y-m-d').' 00:00');
});

it('schedules a project and keeps it off the site until that day', function () {
    $this->actingAs(User::factory()->create());

    $component = fillRequiredProjectFields(Livewire::test('pages::projects.form'))
        ->set('form.slug', 'programado')
        ->set('form.published_on', now('America/Caracas')->addWeek()->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    $project = Project::query()->sole();
    expect($project)->isScheduled()->toBeTrue()->isPublished()->toBeFalse();

    $this->get(route('projects.show', $project))->assertNotFound();
});

it('loads the publication day in the edit form and can send the project back to draft', function () {
    $project = Project::factory()->create(['published_at' => '2026-11-10 04:00:00']);
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::projects.form', ['project' => $project])
        ->assertSet('form.published_on', '2026-11-10')
        ->set('form.published_on', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($project->refresh()->published_at)->toBeNull();
});
