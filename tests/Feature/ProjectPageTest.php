<?php

use App\Models\Project;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withSession(['locale' => 'es']);
});

it('renders a project case study in the visitor language', function (string $locale, string $heading) {
    Project::factory()->create([
        'slug' => 'registro-congresos',
        'title' => ['es' => 'Una plataforma de registro', 'en' => 'One registration platform'],
    ]);

    $response = $this->withSession(['locale' => $locale])->get('/proyectos/registro-congresos');

    $response->assertOk()
        ->assertSee("<html lang=\"{$locale}\"", false)
        ->assertSeeText($heading);
})->with([
    'spanish' => ['es', 'Una plataforma de registro'],
    'english' => ['en', 'One registration platform'],
]);

it('falls back to spanish when a field has no english translation', function () {
    Project::factory()->create([
        'slug' => 'kopaplay',
        'summary' => ['es' => 'Arquitectura para más de 12 ligas.', 'en' => null],
    ]);

    $this->withSession(['locale' => 'en'])->get('/proyectos/kopaplay')
        ->assertOk()
        ->assertSeeText('Arquitectura para más de 12 ligas.');
});

it('returns 404 for an unknown project', function () {
    $this->get('/proyectos/no-existe')->assertNotFound();
});

it('returns 404 for an unpublished project', function () {
    Project::factory()->unpublished()->create(['slug' => 'borrador']);

    $this->get('/proyectos/borrador')->assertNotFound();
});

it('links to the previous and next projects in order, wrapping around the list', function (string $url, string $previousUrl, string $nextUrl) {
    Project::factory()->count(3)->sequence(
        ['slug' => 'squaad', 'sort_order' => 3],
        ['slug' => 'registro-congresos', 'sort_order' => 1],
        ['slug' => 'kopaplay', 'sort_order' => 2],
    )->create();

    $response = $this->get($url);

    $response->assertSee('href="'.url($previousUrl).'" rel="prev"', false)
        ->assertSee('href="'.url($nextUrl).'" rel="next"', false);
})->with([
    'first project' => ['/proyectos/registro-congresos', '/proyectos/squaad', '/proyectos/kopaplay'],
    'last project' => ['/proyectos/squaad', '/proyectos/kopaplay', '/proyectos/registro-congresos'],
]);

it('skips unpublished projects in the previous and next links', function () {
    Project::factory()->count(3)->sequence(
        ['slug' => 'primero', 'sort_order' => 1],
        ['slug' => 'oculto', 'sort_order' => 2, 'is_published' => false],
        ['slug' => 'tercero', 'sort_order' => 3],
    )->create();

    $this->get('/proyectos/primero')
        ->assertSee('href="'.url('/proyectos/tercero').'" rel="next"', false)
        ->assertDontSee(url('/proyectos/oculto'), false);
});

it('hides the live link when the project has no public url', function () {
    Project::factory()->create(['slug' => 'kopaplay']);

    $this->get('/proyectos/kopaplay')->assertOk()->assertDontSeeText('En vivo');
});

it('links to the live platform when the project has a public url', function () {
    Project::factory()->live()->create(['slug' => 'kopaplay']);

    $this->get('/proyectos/kopaplay')->assertOk()
        ->assertSeeText('En vivo')
        ->assertSee('href="https://example.com"', false);
});

it('links each published home project card to its case study and hides drafts', function () {
    Project::factory()->create(['slug' => 'registro-congresos']);
    Project::factory()->unpublished()->create(['slug' => 'borrador']);

    $this->get('/')->assertOk()
        ->assertSee('href="'.url('/proyectos/registro-congresos').'"', false)
        ->assertDontSee(url('/proyectos/borrador'), false);
});

it('shows the project screenshot on its case study', function () {
    Project::factory()->create(['slug' => 'kopaplay', 'image' => 'projects/KOPAPLAY.jpg']);

    $this->get('/proyectos/kopaplay')->assertOk()
        ->assertSee('src="'.Storage::disk('public')->url('projects/KOPAPLAY.jpg').'"', false);
});

it('shows the build screenshots on the case study', function () {
    Project::factory()->create(['slug' => 'kopaplay', 'built_images' => ['projects/KOPAPLAY-1.jpg']]);

    $this->get('/proyectos/kopaplay')->assertOk()
        ->assertSee('src="'.Storage::disk('public')->url('projects/KOPAPLAY-1.jpg').'"', false);
});

it('shows a generic screenshot marker in the visitor language wherever an image is missing', function (string $locale, string $marker) {
    Project::factory()->create(['slug' => 'kopaplay']);

    $this->withSession(['locale' => $locale]);

    $this->get('/')->assertOk()->assertSeeText($marker);
    expect(substr_count($this->get('/proyectos/kopaplay')->assertOk()->getContent(), 'aria-label="'.$marker.'"'))->toBe(3);
})->with([
    'spanish' => ['es', '[CAPTURA]'],
    'english' => ['en', '[CAPTURE]'],
]);

it('lists every published project in order on the projects page', function () {
    Project::factory()->count(5)->sequence(
        ['slug' => 'quinto', 'sort_order' => 5, 'title' => ['es' => 'Quinto proyecto', 'en' => 'Fifth']],
        ['slug' => 'primero', 'sort_order' => 1, 'title' => ['es' => 'Primer proyecto', 'en' => 'First']],
        ['slug' => 'tercero', 'sort_order' => 3, 'title' => ['es' => 'Tercer proyecto', 'en' => 'Third']],
        ['slug' => 'cuarto', 'sort_order' => 4, 'title' => ['es' => 'Cuarto proyecto', 'en' => 'Fourth']],
        ['slug' => 'oculto', 'sort_order' => 2, 'title' => ['es' => 'Proyecto oculto', 'en' => 'Hidden'], 'is_published' => false],
    )->create();

    $this->get('/proyectos')->assertOk()
        ->assertSeeTextInOrder(['Primer proyecto', 'Tercer proyecto', 'Cuarto proyecto', 'Quinto proyecto'])
        ->assertDontSeeText('Proyecto oculto');
});

it('shows at most three projects on the home page and links to the rest', function () {
    Project::factory()->count(4)->sequence(
        ['sort_order' => 1, 'title' => ['es' => 'Primer proyecto', 'en' => 'First']],
        ['sort_order' => 2, 'title' => ['es' => 'Segundo proyecto', 'en' => 'Second']],
        ['sort_order' => 3, 'title' => ['es' => 'Tercer proyecto', 'en' => 'Third']],
        ['sort_order' => 4, 'title' => ['es' => 'Cuarto proyecto', 'en' => 'Fourth']],
    )->create();

    $this->get('/')->assertOk()
        ->assertSeeTextInOrder(['Primer proyecto', 'Segundo proyecto', 'Tercer proyecto'])
        ->assertDontSeeText('Cuarto proyecto')
        ->assertSee('href="'.url('/proyectos').'"', false)
        ->assertSeeText('Ver todos los proyectos');
});

it('hides the link to all projects when the home page already shows them all', function () {
    Project::factory()->count(3)->create();

    $this->get('/')->assertOk()->assertDontSeeText('Ver todos los proyectos');
});

it('links to the projects page from the nav and marks it as current there', function () {
    $response = $this->get('/proyectos')->assertOk();

    expect($response->getContent())->toMatch('#href="'.preg_quote(url('/proyectos'), '#').'"\s+aria-current="page"#');
});
