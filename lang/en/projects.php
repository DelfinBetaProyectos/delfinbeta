<?php

/*
| Static content: will be replaced by the translatable Project model.
| Keyed by slug (/proyectos/{slug}). Each item mirrors the future model
| fields: card (label, title, summary, capture, code) and case study (case).
*/

return [
    'items' => [
        'registro-congresos' => [
            'label' => 'Laravel · Payments · ES/EN',
            'title' => 'International conference registration',
            'summary' => 'One profile for every conference, in two languages. It now runs 7 to 10 conferences a year and more than 3,000 attendees.',
            'capture' => '[SCREENSHOT] Registration platform',
            'code' => null,
            'case' => [
                'breadcrumb' => 'Conference registration',
                'title' => 'One registration platform for every conference of a banking federation',
                'lead' => 'One profile per attendee, in Spanish and English, with built-in payments. I built it as lead developer at Kubilabs and I keep it up to date.',
                'role' => 'Lead developer',
                'period' => '2022 – 2023 · updates to this day',
                'stack' => 'Laravel · MySQL · payment gateways',
                'live_url' => null,
                'capture' => '[SCREENSHOT] Registration form on desktop',
                'context' => 'A Latin American banking federation runs several conferences a year for participants from across the region. [To complete: how registration worked before the platform.]',
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
            'label' => 'Architecture · Laravel · AWS',
            'title' => 'KOPAPLAY · Soccer tournaments',
            'summary' => 'Architecture for a sports management platform used by more than 12 leagues in the US.',
            'capture' => '[SCREENSHOT] KOPAPLAY',
            'code' => null,
            'case' => [
                'breadcrumb' => 'KOPAPLAY',
                'title' => 'Architecture for soccer tournament management',
                'lead' => 'Architecture for a sports management platform used by more than 12 leagues in the US.',
                'role' => 'Solutions architect',
                'period' => '2024 – 2026',
                'stack' => 'Laravel · AWS',
                'live_url' => null,
                'capture' => '[SCREENSHOT] KOPAPLAY on desktop',
                'context' => '[To complete: what KOPAPLAY is and who uses it.]',
                'problem' => '[To complete: the specific problem to solve.]',
                'built' => [
                    'Platform architecture.',
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
    ],
];
