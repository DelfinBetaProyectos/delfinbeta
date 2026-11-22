<x-layouts::site title="Guía UI">
    @php
        $swatches = [
            ['name' => 'Marino', 'role' => 'fondo principal', 'hex' => '#001B48', 'token' => 'navy', 'class' => 'bg-navy'],
            ['name' => 'Azul profundo', 'role' => 'secciones, tarjetas y enlaces en claro', 'hex' => '#004581', 'token' => 'deep', 'class' => 'bg-deep'],
            ['name' => 'Azul mar', 'role' => 'detalles, ola y bordes', 'hex' => '#018ABD', 'token' => 'sea', 'class' => 'bg-sea'],
            ['name' => 'Celeste', 'role' => 'acento y botón primario en oscuro', 'hex' => '#97CBDC', 'token' => 'ice', 'class' => 'bg-ice'],
            ['name' => 'Bruma', 'role' => 'fondo claro y texto en oscuro', 'hex' => '#DDE8F0', 'token' => 'mist', 'class' => 'bg-mist'],
            ['name' => 'Texto suave', 'role' => 'texto secundario en oscuro', 'hex' => '#A9C3D6', 'token' => 'muted', 'class' => 'bg-muted'],
            ['name' => 'Tinta', 'role' => 'texto secundario en claro', 'hex' => '#35506A', 'token' => 'ink', 'class' => 'bg-ink'],
            ['name' => 'Borde oscuro', 'role' => 'tarjetas sobre Marino', 'hex' => '#1A4A85', 'token' => 'line-dark', 'class' => 'bg-line-dark'],
            ['name' => 'Borde claro', 'role' => 'tarjetas sobre blanco', 'hex' => '#C9DAE6', 'token' => 'line-light', 'class' => 'bg-line-light'],
        ];
    @endphp

    <div class="bg-mist text-navy">
        <div class="mx-auto flex max-w-site flex-col gap-20 px-4 py-16 sm:px-6">
            <x-section-heading
                on="light"
                :level="1"
                eyebrow="Guía UI · v0.2 · Laravel + Tailwind v4"
                title="Tokens y componentes"
                description="Paleta mar abierto. Botón primario en Celeste con texto Marino. Azul mar solo para detalles y cifras grandes."
            />

            {{-- Color --}}
            <section class="flex flex-col gap-6">
                <h2 class="font-display text-2xl font-bold">Color</h2>
                <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($swatches as $swatch)
                        <li class="overflow-hidden rounded-card border border-line-light bg-white">
                            <div class="h-20 {{ $swatch['class'] }}"></div>
                            <div class="flex flex-col gap-1 p-3">
                                <p class="text-sm font-medium">{{ $swatch['name'] }} · <span class="font-normal text-ink">{{ $swatch['role'] }}</span></p>
                                <p class="font-mono text-xs text-ink">{{ $swatch['hex'] }} · {{ $swatch['token'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Tipografía --}}
            <section class="flex flex-col gap-6">
                <h2 class="font-display text-2xl font-bold">Tipografía</h2>
                <dl class="grid gap-6 rounded-card border border-line-light bg-white p-6 md:grid-cols-[12rem_1fr] md:p-8">
                    <dt class="font-mono text-xs text-ink">H1 · Space Grotesk 700</dt>
                    <dd class="font-display text-5xl font-bold tracking-tight md:text-7xl">Dayan Betancourt</dd>

                    <dt class="font-mono text-xs text-ink">H2 · Space Grotesk 700</dt>
                    <dd class="font-display text-3xl font-bold tracking-tight md:text-[2.75rem]">Proyectos seleccionados</dd>

                    <dt class="font-mono text-xs text-ink">H3 · Space Grotesk 700</dt>
                    <dd class="font-display text-xl font-bold md:text-2xl">Registro a congresos internacionales</dd>

                    <dt class="font-mono text-xs text-ink">Texto · Inter 400/500</dt>
                    <dd class="text-[17px]">Más de <x-years-of-experience /> años diseñando y construyendo plataformas en la nube de punta a punta, con IA en todo el ciclo de desarrollo.</dd>

                    <dt class="font-mono text-xs text-ink">Etiqueta · Fira Code 500</dt>
                    <dd class="font-mono text-sm font-medium text-deep">02 · Proyectos · Laravel · AWS</dd>
                </dl>
            </section>

            {{-- Componentes --}}
            <section class="flex flex-col gap-6">
                <h2 class="font-display text-2xl font-bold">Componentes</h2>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="flex flex-col gap-6 rounded-card bg-navy p-6 text-mist">
                        <p class="font-mono text-xs text-muted">Sobre fondo oscuro</p>

                        <div class="flex flex-wrap gap-3">
                            <x-button>x-button primary</x-button>
                            <x-button variant="secondary">x-button secondary</x-button>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-tag>Laravel</x-tag>
                            <x-tag>AWS</x-tag>
                            <x-tag>GitHub Actions</x-tag>
                        </div>

                        <x-project-card label="x-project-card" title="Nombre del proyecto" summary="Una línea con el resultado, no la lista de tecnologías." href="#" />

                        <x-talk-card meta="x-talk-card · 45 min" title="Título de la charla" summary="Descripción breve de la charla." />

                        <x-input label="x-input" name="email" type="email" placeholder="tu@correo.com" />
                        <x-select label="x-select" name="topic" :options="['a' => 'Opción A', 'b' => 'Opción B']" />
                        <x-input label="x-input type=textarea" name="message" type="textarea" />

                        <x-photo-placeholder variant="dark" aspect="16/9" label="x-photo-placeholder variant=dark" />
                        <x-photo-placeholder variant="deep" aspect="1/1" label="x-photo-placeholder variant=deep" />
                    </div>

                    <div class="flex flex-col gap-6 rounded-card border border-line-light bg-white p-6">
                        <p class="font-mono text-xs text-ink">Sobre fondo claro</p>

                        <div class="flex flex-wrap gap-3">
                            <x-button variant="dark">x-button dark</x-button>
                            <x-button variant="outline">x-button outline</x-button>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <x-tag on="light">Laravel</x-tag>
                            <x-tag on="light">AWS</x-tag>
                        </div>

                        <p>Un enlace en texto corrido usa <a href="#" class="text-deep underline underline-offset-2 hover:no-underline">Azul profundo</a>, nunca Azul mar.</p>

                        <x-post-card pillar="Pilar" date="2026-11-19" title="Título del artículo" summary="Resumen de dos líneas." href="#" />

                        <ol class="border-l-2 border-ice">
                            <x-timeline-item period="2008" role="x-timeline-item" />
                            <x-timeline-item period="Hoy" role="Rol actual" />
                        </ol>

                        <x-photo-placeholder label="x-photo-placeholder · mientras llegan las fotos" />
                    </div>
                </div>
            </section>

            <section class="flex flex-col gap-6">
                <x-section-heading on="light" eyebrow="02 · x-section-heading" title="Proyectos seleccionados" description="Encabezado de sección con etiqueta y bajada opcional." />
            </section>
        </div>
    </div>
</x-layouts::site>
