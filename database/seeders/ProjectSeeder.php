<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the portfolio projects (formerly static in lang/{es,en}/projects.php).
     * Safe to re-run: projects are matched by slug.
     */
    public function run(): void
    {
        foreach ($this->projects() as $project) {
            Project::updateOrCreate(['slug' => $project['slug']], $project);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function projects(): array
    {
        return [
            [
                'slug' => 'registro-congresos',
                'sort_order' => 1,
                'published_at' => '2026-11-22 00:00:00',
                'title' => [
                    'es' => 'Plataforma de registro para todos los congresos de una federación',
                    'en' => 'Registration platform for every conference of a federation',
                ],
                'summary' => [
                    'es' => 'Un solo perfil para todos los congresos, en dos idiomas. Hoy gestiona entre 7 y 10 congresos al año y más de 3.000 inscritos.',
                    'en' => 'One profile for every conference, in two languages. It now runs 7 to 10 conferences a year and more than 3,000 attendees.',
                ],
                'label' => [
                    'es' => 'Laravel · Pagos · ES/EN',
                    'en' => 'Laravel · Payments · ES/EN',
                ],
                'image' => 'projects/CONGRESOS01.jpg',
                'role' => [
                    'es' => 'Desarrolladora principal',
                    'en' => 'Lead developer',
                ],
                'period' => [
                    'es' => '2022 – 2023 · actualizaciones hasta hoy',
                    'en' => '2022 – 2023 · updates to this day',
                ],
                'stack' => [
                    'es' => 'Laravel · MySQL · pasarelas de pago',
                    'en' => 'Laravel · MySQL · payment gateways',
                ],
                'live_url' => null,
                'context' => [
                    'es' => 'Una federación latinoamericana organiza varios congresos al año para profesionales de toda la región, junto con sus asociaciones miembro y empresas aliadas.',
                    'en' => 'A Latin American federation runs several conferences a year for professionals from across the region, together with its member associations and partner companies.',
                ],
                'problem' => [
                    'es' => 'Cada congreso necesitaba su propio registro, sus pagos y su atención en dos idiomas. Quien asistía a varios tenía que registrarse una y otra vez, y la federación no tenía una vista única de sus asistentes ni de los ingresos de cada evento.',
                    'en' => 'Each conference needed its own registration, payments and support in two languages. Attendees going to several had to sign up again every time, and the federation had no single view of its attendees or of each event\'s revenue.',
                ],
                'built' => [
                    'es' => [
                        'Un perfil único por asistente que sirve para todos los congresos, con su trayectoria y su nivel de miembro.',
                        'Registro e interfaz completos en español e inglés.',
                        'Varias pasarelas de pago dentro del flujo de inscripción, con estado de pago y comprobantes en PDF.',
                        'Networking y mensajes entre asistentes de cada congreso.',
                        'Panel de administración con inscripciones, pagos e ingresos por congreso en tiempo real.',
                        'Gestión de asociaciones, empresas aliadas y staff.',
                    ],
                    'en' => [
                        'A single attendee profile that works for every conference, with their track record and membership tier.',
                        'Registration and interface fully in Spanish and English.',
                        'Several payment gateways inside the registration flow, with payment status and PDF receipts.',
                        'Networking and messaging between attendees of each conference.',
                        'Admin dashboard with registrations, payments and revenue per conference in real time.',
                        'Management of member associations, partner companies and staff.',
                    ],
                ],
                'built_images' => [
                    'projects/CONGRESOS02.jpg',
                    'projects/CONGRESOS03.jpg',
                ],
                'stats' => [
                    'es' => [
                        [
                            'value' => '7–10',
                            'label' => 'congresos al año',
                        ],
                        [
                            'value' => '+3.000',
                            'label' => 'inscritos',
                        ],
                        [
                            'value' => '91',
                            'label' => 'congresos desde 2016',
                        ],
                    ],
                    'en' => [
                        [
                            'value' => '7–10',
                            'label' => 'conferences a year',
                        ],
                        [
                            'value' => '3,000+',
                            'label' => 'attendees',
                        ],
                        [
                            'value' => '91',
                            'label' => 'conferences since 2016',
                        ],
                    ],
                ],
                'result' => [
                    'es' => 'La plataforma sigue en producción y la mantengo con actualizaciones para cada nueva edición.',
                    'en' => 'The platform is still in production and I maintain it with updates for every new edition.',
                ],
            ],
            [
                'slug' => 'kopaplay',
                'sort_order' => 2,
                'published_at' => '2026-11-22 00:00:00',
                'title' => [
                    'es' => 'KOPAPLAY: Arquitectura para la gestión de torneos de fútbol',
                    'en' => 'KOPAPLAY: Architecture for soccer tournament management',
                ],
                'summary' => [
                    'es' => 'Arquitectura de una plataforma de gestión deportiva usada por más de 12 ligas en EE. UU.',
                    'en' => 'Architecture for a sports management platform used by more than 12 leagues in the US.',
                ],
                'label' => [
                    'es' => 'Arquitectura · Laravel · AWS',
                    'en' => 'Architecture · Laravel · AWS',
                ],
                'image' => 'projects/KOPAPLAY01.jpg',
                'role' => [
                    'es' => 'Arquitecta de soluciones',
                    'en' => 'Solutions architect',
                ],
                'period' => [
                    'es' => '2024 – 2026',
                    'en' => '2024 – 2026',
                ],
                'stack' => [
                    'es' => 'Laravel · AWS',
                    'en' => 'Laravel · AWS',
                ],
                'live_url' => 'https://play.kopaplay.com',
                'context' => [
                    'es' => 'KOPAPLAY es una plataforma para organizar torneos de fútbol. Ligas, clubes y organizaciones la usan para inscribir equipos, armar el calendario y publicar resultados, desde torneos de un día de 3v3, 5v5 o futsal hasta ligas de temporada de varios meses, juveniles y de adultos, en inglés y en español.',
                    'en' => 'KOPAPLAY is a platform for running soccer tournaments. Leagues, clubs and organizations use it to register teams, build the schedule and publish results, from one-day 3v3, 5v5 or futsal tournaments to season-long leagues, for youth and adults, in English and Spanish.',
                ],
                'problem' => [
                    'es' => 'Un solo torneo puede llegar a 90 categorías por año de nacimiento, nivel y género, casi 900 equipos y más de 2.000 jugadores, repartidos en varias sedes y meses de partidos. Coordinar inscripciones, cobros, calendario y resultados con hojas de cálculo no escala, y cada organización necesita sus propios administradores y su propia marca.',
                    'en' => 'A single tournament can reach 90 categories by birth year, level and gender, almost 900 teams and more than 2,000 players, spread across several venues and months of games. Handling registrations, payments, scheduling and results in spreadsheets does not scale, and each organization needs its own admins and its own branding.',
                ],
                'built' => [
                    'es' => [
                        'Arquitectura de la plataforma en Laravel sobre AWS.',
                        'Organizaciones con sus propios administradores, managers y directores de equipo.',
                        'Torneos y ligas de cualquier formato (fútbol, futsal, 3v3, 5v5), con divisiones por año de nacimiento, nivel y categoría, y gestión de sedes y canchas.',
                        'Calendario oficial de partidos, centro de partidos para cargar resultados y fichas de partido.',
                        'Inscripción y pago de equipos, con descuentos, cupones y códigos de referido.',
                        'Exportación de equipos y calendarios a Excel y PDF.',
                        'Página pública de cada torneo con categorías, equipos, sedes y calendario, en inglés y español.',
                    ],
                    'en' => [
                        'Platform architecture with Laravel on AWS.',
                        'Organizations with their own admins, managers and team directors.',
                        'Tournaments and leagues in any format (soccer, futsal, 3v3, 5v5), with divisions by birth year, level and category, plus venue and field management.',
                        'Official game schedule, a match center to enter results, and match cards.',
                        'Team registration and payment, with discounts, coupons and referral codes.',
                        'Team and schedule exports to Excel and PDF.',
                        'A public page for each tournament with categories, teams, venues and schedule, in English and Spanish.',
                    ],
                ],
                'built_images' => [
                    'projects/KOPAPLAY02.jpg',
                    'projects/KOPAPLAY03.jpg',
                ],
                'stats' => [
                    'es' => [
                        [
                            'value' => '+12',
                            'label' => 'ligas en EE. UU.',
                        ],
                        [
                            'value' => '+15',
                            'label' => 'torneos en 2026',
                        ],
                        [
                            'value' => '+880',
                            'label' => 'equipos en un solo torneo',
                        ],
                    ],
                    'en' => [
                        [
                            'value' => '12+',
                            'label' => 'leagues in the US',
                        ],
                        [
                            'value' => '15+',
                            'label' => 'tournaments in 2026',
                        ],
                        [
                            'value' => '880+',
                            'label' => 'teams in a single tournament',
                        ],
                    ],
                ],
                'result' => [
                    'es' => 'KOPAPLAY sigue en producción: en 2026 ya suma más de 15 torneos, más de 1.300 equipos y 3.200 jugadores, con inscripciones, cobros, calendario y resultados en un solo lugar.',
                    'en' => 'KOPAPLAY is still in production: in 2026 it already counts more than 15 tournaments, 1,300+ teams and 3,200+ players, with registrations, payments, scheduling and results in one place.',
                ],
            ],
            [
                'slug' => 'squaad',
                'sort_order' => 3,
                'published_at' => '2026-11-22 00:00:00',
                'title' => [
                    'es' => 'SQUAAD: Gestión y cobros para academias deportivas',
                    'en' => 'SQUAAD: Management and billing for sports academies',
                ],
                'summary' => [
                    'es' => 'Cobros recurrentes con Authorize.net para un SaaS que usan más de 15 academias deportivas en Miami.',
                    'en' => 'Recurring billing with Authorize.net for a SaaS used by more than 15 sports academies in Miami.',
                ],
                'label' => [
                    'es' => 'SaaS · Authorize.net · AWS',
                    'en' => 'SaaS · Authorize.net · AWS',
                ],
                'image' => 'projects/SQUAAD01.jpg',
                'role' => [
                    'es' => 'Arquitecta de soluciones',
                    'en' => 'Solutions architect',
                ],
                'period' => [
                    'es' => '2024 – 2026',
                    'en' => '2024 – 2026',
                ],
                'stack' => [
                    'es' => 'SaaS · Authorize.net · AWS',
                    'en' => 'SaaS · Authorize.net · AWS',
                ],
                'live_url' => 'https://pv.squaad.app',
                'context' => [
                    'es' => 'SQUAAD es un SaaS de gestión deportiva con sede en Miami. Lo usan academias, escuelas, clubes y organizaciones deportivas, sobre todo academias de fútbol y futsal, campamentos y ligas juveniles del sur de Florida, en inglés y en español.',
                    'en' => 'SQUAAD is a sports management SaaS based in Miami. It is used by academies, schools, clubs and sports organizations, mostly soccer and futsal academies, camps and youth leagues in South Florida, in English and Spanish.',
                ],
                'problem' => [
                    'es' => 'Una academia deportiva cobra mensualidades, inscripciones a campamentos y programas, y además necesita contratos y exenciones firmados, control de asistencia y comunicación con las familias. Con herramientas sueltas, cada cobro atrasado y cada formulario en papel se convierte en trabajo administrativo.',
                    'en' => 'A sports academy collects monthly fees and camp and program registrations, and it also needs signed contracts and waivers, attendance tracking and communication with families. With scattered tools, every late payment and every paper form turns into admin work.',
                ],
                'built' => [
                    'es' => [
                        'Planes de suscripción flexibles con pagos recurrentes, descuentos y promociones.',
                        'Cobros con tarjeta con Authorize.net, facturación integrada y recordatorios automáticos de pago.',
                        'Firma digital de contratos y exenciones.',
                        'Gestión de jugadores, actividades y torneos, con seguimiento de asistencia.',
                        'Reportes de transacciones exportables a Excel.',
                    ],
                    'en' => [
                        'Flexible subscription plans with recurring payments, discounts and promotions.',
                        'Card payments with Authorize.net, built-in invoicing and automated payment reminders.',
                        'Digital signing of contracts and waivers.',
                        'Player, activity and tournament management, with attendance tracking.',
                        'Transaction reports exportable to Excel.',
                    ],
                ],
                'built_images' => [
                    'projects/SQUAAD02.jpg',
                    'projects/SQUAAD03.jpg',
                ],
                'stats' => [
                    'es' => [
                        [
                            'value' => '+15',
                            'label' => 'academias en Miami',
                        ],
                        [
                            'value' => '+400',
                            'label' => 'transacciones al mes',
                        ],
                        [
                            'value' => '185',
                            'label' => 'jugadores por academia',
                        ],
                        [
                            'value' => '85',
                            'label' => 'suscripciones activas por academia',
                        ],
                    ],
                    'en' => [
                        [
                            'value' => '15+',
                            'label' => 'academies in Miami',
                        ],
                        [
                            'value' => '400+',
                            'label' => 'transactions a month',
                        ],
                        [
                            'value' => '185',
                            'label' => 'players per academy',
                        ],
                        [
                            'value' => '85',
                            'label' => 'active subscriptions per academy',
                        ],
                    ],
                ],
                'result' => [
                    'es' => 'SQUAAD sigue en producción, lo usan más de 15 academias en Miami y sigue sumando funciones como los pagos con cuenta bancaria, un marketplace para clubes y la integración con QuickBooks.',
                    'en' => 'SQUAAD is still in production, used by more than 15 academies in Miami, and keeps adding features such as bank account payments, a marketplace for clubs and the QuickBooks integration.',
                ],
            ],
        ];
    }
}
