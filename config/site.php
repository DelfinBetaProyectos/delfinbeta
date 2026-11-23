<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | Public pages have a single, unprefixed URL. The language comes from the
    | signed-in user, then the session, then the browser. The first locale is
    | the fallback when the browser language is not supported.
    |
    */

    'locales' => ['es', 'en'],

    /*
    | Each language's name written in that language, used by language pickers.
    */

    'locale_names' => [
        'es' => 'Español',
        'en' => 'English',
    ],

    /*
    |--------------------------------------------------------------------------
    | Career start
    |--------------------------------------------------------------------------
    |
    | Used to compute the "Más de N años" copy so it never goes stale.
    |
    */

    'career_started_at' => '2008-01-01',

    /*
    |--------------------------------------------------------------------------
    | AraguaneyCode
    |--------------------------------------------------------------------------
    |
    | Every project inquiry is routed to AraguaneyCode (no prices on this site).
    |
    */

    'araguaneycode_url' => env('SITE_ARAGUANEYCODE_URL', 'https://araguaneycode.com/'),

    /*
    | Project inquiries from the contact form are copied to this address.
    */

    'araguaneycode_email' => env('SITE_ARAGUANEYCODE_EMAIL', 'info@araguaneycode.com'),

    /*
    |--------------------------------------------------------------------------
    | Social links
    |--------------------------------------------------------------------------
    |
    | Empty values are not rendered.
    |
    */

    'social' => [
        'LinkedIn' => env('SITE_LINKEDIN_URL', 'https://www.linkedin.com/in/delfinbeta'),
        'GitHub' => env('SITE_GITHUB_URL', 'https://github.com/delfinbeta'),
        'Medium' => env('SITE_MEDIUM_URL', 'https://medium.com/@delfinbeta'),
        'DEV' => env('SITE_DEV_URL', 'https://dev.to/delfinbeta'),
        'Instagram' => env('SITE_INSTAGRAM_URL', 'https://instagram.com/delfinbeta'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Source code
    |--------------------------------------------------------------------------
    |
    | Repository of this site, linked from the footer.
    |
    */

    'source_url' => env('SITE_SOURCE_URL', 'https://github.com/DelfinBetaProyectos/delfinbeta'),

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    */

    'email' => env('SITE_EMAIL', 'hola@delfinbeta.tech'),

    /*
    | Promised reply time for contact form messages, and the timezone the
    | notification email shows the date in (Maracay, UTC-4).
    */

    'reply_within_hours' => 48,

    'timezone' => 'America/Caracas',

    /*
    |--------------------------------------------------------------------------
    | Resume
    |--------------------------------------------------------------------------
    |
    | One PDF per locale, stored on the "public" disk.
    |
    */

    'resumes' => [
        'es' => 'resume/Dayan_Betancourt_ATS_ES.pdf',
        'en' => 'resume/Dayan_Betancourt_ATS_EN.pdf',
    ],

];
