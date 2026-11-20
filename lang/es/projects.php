<?php

/*
| Contenido estático: se reemplazará por el modelo Project con traducciones.
| Indexado por slug (/proyectos/{slug}). Cada item tiene la forma de los
| campos futuros del modelo: tarjeta (label, title, summary, capture, code)
| y caso (case).
*/

return [
    'items' => [
        'felaban' => [
            'label' => 'Laravel · Pagos · ES/EN',
            'title' => 'FELABAN · Registro a congresos',
            'summary' => 'Un solo perfil para todos los congresos, en dos idiomas. Hoy gestiona entre 7 y 10 congresos al año y más de 3.000 inscritos.',
            'capture' => '[CAPTURA] registro.felaban.net',
            'code' => null,
            'case' => [
                'breadcrumb' => 'FELABAN',
                'title' => 'Una plataforma de registro para todos los congresos de FELABAN',
                'lead' => 'Un solo perfil para cada asistente, en español e inglés, con pagos integrados. La creé como desarrolladora principal en Kubilabs y la sigo actualizando.',
                'role' => 'Desarrolladora principal',
                'period' => '2022 – 2023 · actualizaciones hasta hoy',
                'stack' => 'Laravel · MySQL · pasarelas de pago',
                'live_url' => 'https://registro.felaban.net',
                'capture' => '[CAPTURA] Formulario de registro en desktop',
                'context' => 'La Federación Latinoamericana de Bancos organiza varios congresos al año para participantes de toda la región. [Completar: cómo se hacía el registro antes de la plataforma.]',
                'problem' => 'Cada congreso necesitaba su propio registro, sus pagos y su atención en dos idiomas. [Completar: el dolor concreto que había que resolver.]',
                'built' => [
                    'Un perfil único por asistente que sirve para todos los congresos.',
                    'Registro e interfaz completos en español e inglés.',
                    'Integraciones de pago dentro del flujo de inscripción.',
                    '[Completar: panel de administración, reportes u otros módulos.]',
                ],
                'built_captures' => ['[CAPTURA] Perfil', '[CAPTURA] Pago'],
                'stats' => [
                    ['value' => '7–10', 'label' => 'congresos al año'],
                    ['value' => '+3.000', 'label' => 'inscritos'],
                    ['value' => '2022', 'label' => 'en uso desde'],
                ],
                'result' => 'La plataforma sigue en producción y la mantengo con actualizaciones para cada nueva edición.',
            ],
        ],
        'kopaplay' => [
            'label' => 'Arquitectura · Laravel Passport · AWS',
            'title' => 'KOPAPLAY · Torneos de fútbol',
            'summary' => 'Arquitectura y módulo de autenticación con Passport para una plataforma de gestión deportiva usada por más de 12 ligas en EE. UU.',
            'capture' => '[CAPTURA] KOPAPLAY',
            'code' => null,
            'case' => [
                'breadcrumb' => 'KOPAPLAY',
                'title' => 'Arquitectura y autenticación para la gestión de torneos de fútbol',
                'lead' => 'Arquitectura y módulo de autenticación con Passport para una plataforma de gestión deportiva usada por más de 12 ligas en EE. UU.',
                'role' => 'Arquitecta de soluciones',
                'period' => '2024 – 2026',
                'stack' => 'Laravel · Passport · AWS',
                'live_url' => null,
                'capture' => '[CAPTURA] KOPAPLAY en desktop',
                'context' => '[Completar: qué es KOPAPLAY y quién lo usa.]',
                'problem' => '[Completar: el problema concreto que había que resolver.]',
                'built' => [
                    'Arquitectura de la plataforma.',
                    'Módulo de autenticación con Laravel Passport.',
                    '[Completar: otros módulos.]',
                ],
                'built_captures' => ['[CAPTURA] Pantalla 1', '[CAPTURA] Pantalla 2'],
                'stats' => [
                    ['value' => '+12', 'label' => 'ligas en EE. UU.'],
                ],
                'result' => '[Completar: estado actual y resultado.]',
            ],
        ],
        'squaad' => [
            'label' => 'SaaS · Authorize.net · AWS',
            'title' => 'SQUAAD · Pagos recurrentes',
            'summary' => 'Cobros recurrentes con Authorize.net para un SaaS que usan más de 15 academias deportivas en Miami.',
            'capture' => '[CAPTURA] SQUAAD',
            'code' => null,
            'case' => [
                'breadcrumb' => 'SQUAAD',
                'title' => 'Cobros recurrentes para academias deportivas',
                'lead' => 'Cobros recurrentes con Authorize.net para un SaaS que usan más de 15 academias deportivas en Miami.',
                'role' => 'Arquitecta de soluciones',
                'period' => '2024 – 2026',
                'stack' => 'SaaS · Authorize.net · AWS',
                'live_url' => null,
                'capture' => '[CAPTURA] SQUAAD en desktop',
                'context' => '[Completar: qué es SQUAAD y quién lo usa.]',
                'problem' => '[Completar: el problema concreto que había que resolver.]',
                'built' => [
                    'Cobros recurrentes con Authorize.net.',
                    '[Completar: otros módulos.]',
                ],
                'built_captures' => ['[CAPTURA] Pantalla 1', '[CAPTURA] Pantalla 2'],
                'stats' => [
                    ['value' => '+15', 'label' => 'academias en Miami'],
                ],
                'result' => '[Completar: estado actual y resultado.]',
            ],
        ],
        'ci-cd' => [
            'label' => 'Caso de método · GitHub Actions',
            'title' => 'De cero a producción con CI/CD',
            'summary' => 'Cómo organizo ramas, Conventional Commits y despliegues automáticos para que cada cambio llegue a producción probado.',
            'capture' => null,
            'code' => "on: push\njobs:\n  test → build → deploy",
            'case' => [
                'breadcrumb' => 'CI/CD',
                'title' => 'De cero a producción con CI/CD',
                'lead' => 'Cómo organizo ramas, Conventional Commits y despliegues automáticos para que cada cambio llegue a producción probado.',
                'role' => 'Caso de método',
                'period' => '[Completar]',
                'stack' => 'GitHub Actions',
                'live_url' => null,
                'capture' => '[CAPTURA] Pipeline en GitHub Actions',
                'context' => '[Completar: en qué tipo de proyectos aplico este método.]',
                'problem' => '[Completar: qué pasa cuando no hay CI/CD.]',
                'built' => [
                    'Estrategia de ramas.',
                    'Conventional Commits.',
                    'Despliegues automáticos: test → build → deploy.',
                ],
                'built_captures' => ['[CAPTURA] Workflow', '[CAPTURA] Despliegue'],
                'stats' => [],
                'result' => '[Completar: resultado del método.]',
            ],
        ],
    ],
];
