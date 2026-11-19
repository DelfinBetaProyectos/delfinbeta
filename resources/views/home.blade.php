<x-layouts::site :title="__('home.meta_title')">
    @php
        $sections = __('nav.sections');
        $projects = __('projects.items');
        $experience = __('experience.items');
        $posts = __('posts.items');
    @endphp

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="mx-auto flex max-w-6xl flex-col gap-8 px-4 pt-20 pb-32 sm:px-6 md:pt-28 md:pb-40">
            <p class="font-mono text-sm font-medium text-ice">{{ __('home.hero.eyebrow') }}</p>

            <h1 class="font-display text-5xl font-bold tracking-tight text-white md:text-7xl">Dayan Betancourt</h1>

            <div class="flex max-w-2xl flex-col gap-3">
                <p class="text-lg text-mist md:text-xl">{{ __('home.hero.lead') }}</p>
                <p class="font-display text-2xl font-bold text-sea md:text-3xl">
                    <x-years-of-experience :text="__('home.hero.experience')" />
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <x-button :href="'#'.$sections['projects']['anchor']">{{ __('home.hero.primary_cta') }}</x-button>
                <x-button variant="secondary" :href="config('site.araguaneycode_url')" rel="noopener" target="_blank">{{ __('home.hero.secondary_cta') }}</x-button>
            </div>
        </div>

        <svg class="absolute inset-x-0 bottom-0 h-16 w-full text-mist md:h-24" viewBox="0 0 1440 96" preserveAspectRatio="none" aria-hidden="true">
            <path fill="none" stroke="var(--color-sea)" stroke-width="2" d="M0 40c120 22 240 22 360 0s240-22 360 0 240 22 360 0 240-22 360 0" />
            <path fill="currentColor" d="M0 56c120 22 240 22 360 0s240-22 360 0 240 22 360 0 240-22 360 0v40H0z" />
        </svg>
    </section>

    {{-- Sobre mí --}}
    <section id="{{ $sections['about']['anchor'] }}" class="scroll-mt-20 bg-mist text-navy">
        <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 sm:px-6 md:grid-cols-[3fr_2fr] md:py-28">
            <div class="flex flex-col gap-6">
                <x-section-heading on="light" :eyebrow="__('home.about.eyebrow')" :title="__('home.about.title')" />

                @foreach (__('home.about.body') as $paragraph)
                    <p class="max-w-2xl text-[17px] text-ink">{{ $paragraph }}</p>
                @endforeach
            </div>

            <x-photo-placeholder :label="__('home.about.photo')" class="aspect-[4/5]" />
        </div>
    </section>

    {{-- Proyectos --}}
    <section id="{{ $sections['projects']['anchor'] }}" class="scroll-mt-20">
        <div class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-20 sm:px-6 md:py-28">
            <x-section-heading :eyebrow="__('home.projects.eyebrow')" :title="__('home.projects.title')" :description="__('home.projects.description')" />

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-project-card :label="$project['label']" :title="$project['title']" :summary="$project['summary']" :href="$project['href']">
                        @foreach ($project['tags'] as $tag)
                            <x-tag>{{ $tag }}</x-tag>
                        @endforeach
                    </x-project-card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Experiencia --}}
    <section id="{{ $sections['experience']['anchor'] }}" class="scroll-mt-20 bg-mist text-navy">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 py-20 sm:px-6 md:grid-cols-[2fr_3fr] md:py-28">
            <x-section-heading on="light" :eyebrow="__('home.experience.eyebrow')" :title="__('home.experience.title')" />

            <ol class="ml-2">
                @foreach ($experience as $item)
                    <x-timeline-item on="light" :period="$item['period']" :role="$item['role']" :organization="$item['organization']">
                        {{ $item['description'] }}
                    </x-timeline-item>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Contenido: solo con 3 o más artículos --}}
    @if (count($posts) >= 3)
        <section id="{{ __('nav.content_anchor') }}" class="scroll-mt-20 bg-white text-navy">
            <div class="mx-auto flex max-w-6xl flex-col gap-12 px-4 py-20 sm:px-6 md:py-28">
                <x-section-heading on="light" :eyebrow="__('home.content.eyebrow')" :title="__('home.content.title')" />

                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-post-card :pillar="$post['pillar']" :date="$post['date']" :title="$post['title']" :summary="$post['summary']" :href="$post['href']" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contacto --}}
    <section id="{{ $sections['contact']['anchor'] }}" class="scroll-mt-20">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 md:py-28">
            <div class="flex flex-col items-start gap-8 rounded-card bg-deep p-8 md:flex-row md:items-end md:justify-between md:p-12">
                <x-section-heading :eyebrow="__('home.contact.eyebrow')" :title="__('home.contact.title')" :description="__('home.contact.description')" />

                <x-button :href="config('site.araguaneycode_url')" rel="noopener" target="_blank" class="shrink-0">{{ __('home.contact.cta') }}</x-button>
            </div>
        </div>
    </section>
</x-layouts::site>
