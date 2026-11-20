<?php

use Illuminate\Support\Facades\Storage;

/**
 * @return array{es: string, en: string}
 */
function resumeUrls(): array
{
    return [
        'es' => Storage::disk('public')->url(config('site.resumes.es')),
        'en' => Storage::disk('public')->url(config('site.resumes.en')),
    ];
}

it('offers the resume in every language from the download dropdown', function () {
    $resumeUrls = resumeUrls();

    $this->withSession(['locale' => 'en'])
        ->get('/mi-historia')
        ->assertSee('<details data-dropdown', false)
        ->assertSee('href="'.$resumeUrls['es'].'" download hreflang="es"', false)
        ->assertSee('href="'.$resumeUrls['en'].'" download hreflang="en"', false)
        ->assertSeeText('Español')
        ->assertSeeText('English');
});

it('links the home contact buttons to the resume files', function () {
    $resumeUrls = resumeUrls();

    $this->get('/')
        ->assertSee('href="'.$resumeUrls['es'].'"', false)
        ->assertSee('href="'.$resumeUrls['en'].'"', false);
});

it('points the resume links to the public storage folder', function () {
    expect(resumeUrls()['es'])->toEndWith('/storage/resume/Dayan_Betancourt_ATS_ES.pdf');
});
