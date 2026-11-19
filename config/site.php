<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | Every public page lives under an explicit prefix (/es, /en). The first
    | locale is the fallback when the browser language is not supported.
    |
    */

    'locales' => ['es', 'en'],

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
    |--------------------------------------------------------------------------
    | Social links
    |--------------------------------------------------------------------------
    |
    | Empty values are not rendered.
    |
    */

    'social' => [
        'GitHub' => env('SITE_GITHUB_URL', 'https://github.com/delfinbeta'),
        'LinkedIn' => env('SITE_LINKEDIN_URL', 'https://www.linkedin.com/in/delfinbeta'),
    ],

];
