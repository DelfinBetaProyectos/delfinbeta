<?php

/*
| Static content: will be replaced by the translatable Project model.
| Keyed by slug (/en/projects/{slug}). Each item mirrors the future model
| fields: card (label, title, summary, capture, code) and case study (case).
*/

return [
    'items' => [
        'felaban' => [
            'label' => 'Laravel · Payments · ES/EN',
            'title' => 'FELABAN · Conference registration',
            'summary' => 'One profile for every conference, in two languages. It now runs 7 to 10 conferences a year and more than 3,000 attendees.',
            'capture' => '[SCREENSHOT] registro.felaban.net',
            'code' => null,
            'case' => [
                'breadcrumb' => 'FELABAN',
                'title' => 'One registration platform for every FELABAN conference',
                'lead' => 'One profile per attendee, in Spanish and English, with built-in payments. I built it as lead developer at Kubilabs and I keep it up to date.',
                'role' => 'Lead developer',
                'period' => '2022 – 2023 · updates to this day',
                'stack' => 'Laravel · MySQL · payment gateways',
                'live_url' => 'https://registro.felaban.net',
                'capture' => '[SCREENSHOT] Registration form on desktop',
                'context' => 'The Latin American Federation of Banks runs several conferences a year for participants from across the region. [To complete: how registration worked before the platform.]',
                'problem' => 'Each conference needed its own registration, payments and support in two languages. [To complete: the specific pain to solve.]',
                'built' => [
                    'A single attendee profile that works for every conference.',
                    'Registration and interface fully in Spanish and English.',
                    'Payment integrations inside the registration flow.',
                    '[To complete: admin panel, reports or other modules.]',
                ],
                'built_captures' => ['[SCREENSHOT] Profile', '[SCREENSHOT] Payment'],
                'stats' => [
                    ['value' => '7–10', 'label' => 'conferences a year'],
                    ['value' => '3,000+', 'label' => 'attendees'],
                    ['value' => '2022', 'label' => 'in use since'],
                ],
                'result' => 'The platform is still in production and I maintain it with updates for every new edition.',
            ],
        ],
        'kopaplay' => [
            'label' => 'Architecture · Laravel Passport · AWS',
            'title' => 'KOPAPLAY · Soccer tournaments',
            'summary' => 'Architecture and Passport authentication module for a sports management platform used by more than 12 leagues in the US.',
            'capture' => '[SCREENSHOT] KOPAPLAY',
            'code' => null,
            'case' => [
                'breadcrumb' => 'KOPAPLAY',
                'title' => 'Architecture and authentication for soccer tournament management',
                'lead' => 'Architecture and Passport authentication module for a sports management platform used by more than 12 leagues in the US.',
                'role' => 'Solutions architect',
                'period' => '2024 – 2026',
                'stack' => 'Laravel · Passport · AWS',
                'live_url' => null,
                'capture' => '[SCREENSHOT] KOPAPLAY on desktop',
                'context' => '[To complete: what KOPAPLAY is and who uses it.]',
                'problem' => '[To complete: the specific problem to solve.]',
                'built' => [
                    'Platform architecture.',
                    'Authentication module with Laravel Passport.',
                    '[To complete: other modules.]',
                ],
                'built_captures' => ['[SCREENSHOT] Screen 1', '[SCREENSHOT] Screen 2'],
                'stats' => [
                    ['value' => '12+', 'label' => 'leagues in the US'],
                ],
                'result' => '[To complete: current status and result.]',
            ],
        ],
        'squaad' => [
            'label' => 'SaaS · Authorize.net · AWS',
            'title' => 'SQUAAD · Recurring payments',
            'summary' => 'Recurring billing with Authorize.net for a SaaS used by more than 15 sports academies in Miami.',
            'capture' => '[SCREENSHOT] SQUAAD',
            'code' => null,
            'case' => [
                'breadcrumb' => 'SQUAAD',
                'title' => 'Recurring billing for sports academies',
                'lead' => 'Recurring billing with Authorize.net for a SaaS used by more than 15 sports academies in Miami.',
                'role' => 'Solutions architect',
                'period' => '2024 – 2026',
                'stack' => 'SaaS · Authorize.net · AWS',
                'live_url' => null,
                'capture' => '[SCREENSHOT] SQUAAD on desktop',
                'context' => '[To complete: what SQUAAD is and who uses it.]',
                'problem' => '[To complete: the specific problem to solve.]',
                'built' => [
                    'Recurring billing with Authorize.net.',
                    '[To complete: other modules.]',
                ],
                'built_captures' => ['[SCREENSHOT] Screen 1', '[SCREENSHOT] Screen 2'],
                'stats' => [
                    ['value' => '15+', 'label' => 'academies in Miami'],
                ],
                'result' => '[To complete: current status and result.]',
            ],
        ],
        'ci-cd' => [
            'label' => 'Method case · GitHub Actions',
            'title' => 'From zero to production with CI/CD',
            'summary' => 'How I organize branches, Conventional Commits and automatic deployments so every change reaches production tested.',
            'capture' => null,
            'code' => "on: push\njobs:\n  test → build → deploy",
            'case' => [
                'breadcrumb' => 'CI/CD',
                'title' => 'From zero to production with CI/CD',
                'lead' => 'How I organize branches, Conventional Commits and automatic deployments so every change reaches production tested.',
                'role' => 'Method case',
                'period' => '[To complete]',
                'stack' => 'GitHub Actions',
                'live_url' => null,
                'capture' => '[SCREENSHOT] Pipeline in GitHub Actions',
                'context' => '[To complete: what kind of projects I apply this method to.]',
                'problem' => '[To complete: what happens without CI/CD.]',
                'built' => [
                    'Branching strategy.',
                    'Conventional Commits.',
                    'Automatic deployments: test → build → deploy.',
                ],
                'built_captures' => ['[SCREENSHOT] Workflow', '[SCREENSHOT] Deployment'],
                'stats' => [],
                'result' => '[To complete: result of the method.]',
            ],
        ],
    ],
];
