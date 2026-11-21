<?php

/*
| Contenido estático: se reemplazará por el modelo Project con traducciones.
| Indexado por slug (/proyectos/{slug}). Cada item tiene la forma de los
| campos futuros del modelo: tarjeta (label, title, summary, capture, code)
| y caso (case).
*/

return [
    'items' => [
        'registro-congresos' => [
            'label' => 'Laravel · Pagos · ES/EN',
            'title' => 'Registro a congresos internacionales',
            'summary' => 'Un solo perfil para todos los congresos, en dos idiomas. Hoy gestiona entre 7 y 10 congresos al año y más de 3.000 inscritos.',
            'capture' => '[CAPTURA] Plataforma de registro',
            'code' => null,
            'case' => [
                'breadcrumb' => 'Registro a congresos',
                'title' => 'Una plataforma de registro para todos los congresos de una federación bancaria',
                'lead' => 'Un solo perfil para cada asistente, en español e inglés, con pagos integrados. La creé como desarrolladora principal en Kubilabs y la sigo actualizando.',
                'role' => 'Desarrolladora principal',
                'period' => '2022 – 2023 · actualizaciones hasta hoy',
                'stack' => 'Laravel · MySQL · pasarelas de pago',
                'live_url' => null,
                'capture' => '[CAPTURA] Formulario de registro en desktop',
                'context' => 'Una federación bancaria latinoamericana organiza varios congresos al año para participantes de toda la región. [Completar: cómo se hacía el registro antes de la plataforma.]',
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
            'label' => 'Arquitectura · Laravel · AWS',
            'title' => 'KOPAPLAY · Torneos de fútbol',
            'summary' => 'Arquitectura de una plataforma de gestión deportiva usada por más de 12 ligas en EE. UU.',
            'capture' => '[CAPTURA] KOPAPLAY',
            'code' => null,
            'case' => [
                'breadcrumb' => 'KOPAPLAY',
                'title' => 'Arquitectura para la gestión de torneos de fútbol',
                'lead' => 'Arquitectura de una plataforma de gestión deportiva usada por más de 12 ligas en EE. UU.',
                'role' => 'Arquitecta de soluciones',
                'period' => '2024 – 2026',
                'stack' => 'Laravel · AWS',
                'live_url' => null,
                'capture' => '[CAPTURA] KOPAPLAY en desktop',
                'context' => '[Completar: qué es KOPAPLAY y quién lo usa.]',
                'problem' => '[Completar: el problema concreto que había que resolver.]',
                'built' => [
                    'Arquitectura de la plataforma.',
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
    ],
];
