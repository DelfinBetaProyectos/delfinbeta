<?php

use Illuminate\Support\Facades\Storage;

it('offers the resume in every language from the download dropdown', function () {
    $this->withSession(['locale' => 'en'])
        ->get('/mi-historia')
        ->assertSee('<details data-dropdown', false)
        ->assertSee('href="'.route('resume.download', 'es').'" download hreflang="es"', false)
        ->assertSee('href="'.route('resume.download', 'en').'" download hreflang="en"', false)
        ->assertSeeText('Español')
        ->assertSeeText('English');
});

it('links the home contact buttons to the resume downloads', function () {
    $this->get('/')
        ->assertSee('href="'.route('resume.download', 'es').'"', false)
        ->assertSee('href="'.route('resume.download', 'en').'"', false);
});

it('downloads the resume of the chosen language', function (string $locale) {
    Storage::fake('public');
    Storage::disk('public')->put(config("site.resumes.{$locale}"), '%PDF-1.4 resume');

    $this->get("/cv/{$locale}")
        ->assertOk()
        ->assertDownload(basename(config("site.resumes.{$locale}")));
})->with(['es', 'en']);

it('answers not found when the resume file is missing', function () {
    Storage::fake('public');

    $this->get('/cv/es')->assertNotFound();
});

it('only serves the site languages', function () {
    $this->get('/cv/fr')->assertNotFound();
});
