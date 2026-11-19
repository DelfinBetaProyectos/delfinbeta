<x-layouts::site :title="__('home.meta_title')">
    @php
        $anchors = collect(__('nav.sections'))->map(fn (array $section): string => $section['anchor']);
        $projects = __('projects.items');
        $talks = __('talks.items');
        $posts = __('posts.items');
        $showContent = count($posts) >= 3;
        $eyebrows = collect(['story', 'projects', 'community', ...($showContent ? ['content'] : []), 'araguaneycode', 'contact'])
            ->values()
            ->mapWithKeys(fn (string $section, int $index): array => [
                $section => sprintf('%02d · %s', $index + 1, __("home.{$section}.eyebrow")),
            ]);
    @endphp

    {{-- Hero --}}
    <section class="mx-auto flex max-w-site flex-wrap items-center gap-14 px-4 pt-16 pb-14 sm:px-6 md:pt-22 md:pb-18">
        <div class="flex min-w-0 flex-[999_1_32rem] flex-col gap-6">
            <p class="font-mono text-sm text-ice">{{ __('home.hero.eyebrow') }}</p>

            <h1 class="font-display text-[clamp(2.75rem,6vw,4.75rem)] leading-[1.02] font-bold tracking-[-0.03em]">Dayan Betancourt</h1>

            <p class="font-display text-[22px] leading-snug font-medium text-mist">
                <x-years-of-experience :text="__('home.hero.role')" />
                <a href="{{ config('site.araguaneycode_url') }}" rel="noopener" target="_blank" class="text-ice underline underline-offset-4 hover:text-mist">AraguaneyCode</a>
            </p>

            <p class="max-w-[600px] text-[19px] text-muted">{{ __('home.hero.lead') }}</p>

            <div class="flex flex-wrap gap-3.5 pt-2">
                <x-button :href="'#'.$anchors['projects']">{{ __('home.hero.primary_cta') }}</x-button>
                <x-button variant="secondary" :href="'#'.$anchors['contact']">{{ __('home.hero.secondary_cta') }}</x-button>
            </div>
        </div>

        <div class="relative min-w-0 flex-[1_1_24rem]">
            <x-photo-placeholder variant="portrait" aspect="4/5" :label="__('home.hero.photo')" />

            <div class="absolute bottom-8 -left-2 rounded-xl border border-line-dark bg-navy px-5 py-4 font-mono text-[13px] leading-[1.7] shadow-[0_18px_40px_rgba(0,0,0,0.45)] sm:-left-6" aria-hidden="true">
                <div class="text-muted">{{ __('home.hero.stack_command') }}</div>
                @foreach (__('home.hero.stack') as $line)
                    <div class="text-ice">{{ $line }}</div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Ola --}}
    <svg viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" class="block h-24 w-full">
        <path d="M0 70 C 180 20, 360 20, 540 60 S 900 110, 1080 60 S 1320 10, 1440 40 L 1440 120 L 0 120 Z" class="fill-deep" />
        <path d="M0 90 C 200 50, 400 60, 600 85 S 1000 115, 1200 80 S 1380 60, 1440 70 L 1440 120 L 0 120 Z" class="fill-sea" opacity="0.45" />
        <path d="M0 40 C 180 -5, 360 0, 540 38 S 900 92, 1080 40 S 1320 -10, 1440 18" fill="none" class="stroke-sea" stroke-width="2" />
    </svg>

    {{-- Cifras --}}
    <section aria-label="{{ __('home.stats.label') }}" class="bg-deep">
        <dl class="mx-auto grid max-w-site grid-cols-[repeat(auto-fit,minmax(13.75rem,1fr))] gap-6 px-4 py-9 sm:px-6">
            @foreach (__('home.stats.items') as $stat)
                <div class="flex flex-col-reverse">
                    <dt class="text-[15px] text-muted">{{ $stat['label'] }}</dt>
                    <dd class="font-display text-[40px] leading-tight font-bold text-ice">
                        <x-years-of-experience :text="$stat['value']" />
                    </dd>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Mi historia --}}
    <section id="{{ $anchors['story'] }}" class="scroll-mt-20 bg-mist text-navy">
        <div class="mx-auto flex max-w-site flex-wrap gap-16 px-4 py-24 sm:px-6">
            <div class="flex min-w-0 flex-[999_1_30rem] flex-col gap-5">
                <x-section-heading on="light" :eyebrow="$eyebrows['story']" :title="__('home.story.title')" />

                @foreach (__('home.story.body') as $paragraph)
                    <p class="text-ink-strong">{{ $paragraph }}</p>
                @endforeach

                <a href="{{ URL::localizedRoute('story') }}" class="font-medium text-deep underline underline-offset-4 hover:no-underline">
                    {{ __('home.story.read_more') }} <span aria-hidden="true">→</span>
                </a>
            </div>

            <ol class="min-w-0 flex-[1_1_24rem] border-l-2 border-ice">
                @foreach (__('home.story.timeline') as $milestone)
                    <x-timeline-item :period="$milestone['period']" :role="$milestone['role']" />
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Proyectos --}}
    <section id="{{ $anchors['projects'] }}" class="mx-auto flex max-w-site scroll-mt-20 flex-col gap-10 px-4 py-24 sm:px-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <x-section-heading :eyebrow="$eyebrows['projects']" :title="__('home.projects.title')" class="max-w-[640px]" />
            <p class="max-w-[380px] text-muted">{{ __('home.projects.description') }}</p>
        </div>

        <div class="grid grid-cols-[repeat(auto-fit,minmax(min(20rem,100%),1fr))] gap-6">
            @foreach ($projects as $project)
                <x-project-card :label="$project['label']" :title="$project['title']" :summary="$project['summary']" :capture="$project['capture']" :href="$project['href']">
                    @if ($project['code'])
                        <x-slot:media>
                            <pre class="font-mono text-[13px] leading-[1.7] text-mist">{{ $project['code'] }}</pre>
                        </x-slot:media>
                    @endif
                </x-project-card>
            @endforeach
        </div>
    </section>

    {{-- Comunidad y charlas --}}
    <section id="{{ $anchors['community'] }}" class="scroll-mt-20 bg-deep">
        <div class="mx-auto flex max-w-site flex-wrap gap-14 px-4 py-24 sm:px-6">
            <div class="flex min-w-0 flex-[1_1_25rem] flex-col gap-5">
                <x-section-heading :eyebrow="$eyebrows['community']" :title="__('home.community.title')" />
                <p class="text-muted">{{ __('home.community.body') }}</p>
                <x-photo-placeholder variant="dark" aspect="16/9" :label="__('home.community.photo')" />
            </div>

            <div class="flex min-w-0 flex-[1_1_27.5rem] flex-col gap-4">
                <h3 class="font-display text-xl font-bold">{{ __('home.community.talks_title') }}</h3>

                @foreach ($talks as $talk)
                    <x-talk-card :meta="$talk['meta']" :title="$talk['title']" :summary="$talk['summary']" />
                @endforeach

                <div class="pt-2">
                    <x-button :href="'#'.$anchors['contact']" class="focus-visible:ring-offset-deep">{{ __('home.community.cta') }}</x-button>
                </div>
            </div>
        </div>
    </section>

    {{-- Contenido: solo con 3 o más artículos --}}
    @if ($showContent)
        <section id="{{ $anchors['content'] }}" class="scroll-mt-20 bg-mist text-navy">
            <div class="mx-auto flex max-w-site flex-col gap-10 px-4 py-24 sm:px-6">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading on="light" :eyebrow="$eyebrows['content']" :title="__('home.content.title')" />
                    <a href="#" class="font-medium text-deep underline underline-offset-4 hover:no-underline">
                        {{ __('home.content.view_all') }} <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(18.75rem,100%),1fr))] gap-6">
                    @foreach ($posts as $post)
                        <x-post-card :pillar="$post['pillar']" :date="$post['date']" :title="$post['title']" :summary="$post['summary']" :href="$post['href']" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- AraguaneyCode --}}
    <section aria-labelledby="araguaneycode-title" class="mx-auto max-w-site px-4 py-24 sm:px-6">
        <div class="flex flex-wrap items-center gap-12 rounded-3xl border border-line-dark bg-deep p-8 md:p-14">
            <div role="img" aria-label="AraguaneyCode" class="flex h-[120px] w-[200px] flex-none items-center justify-center rounded-xl border border-dashed border-line-dashed bg-navy p-4 text-center font-mono text-xs text-muted">
                {{ __('home.araguaneycode.logo') }}
            </div>

            <div class="flex min-w-0 flex-[999_1_26rem] flex-col gap-3.5">
                <p class="font-mono text-sm font-medium text-ice">{{ $eyebrows['araguaneycode'] }}</p>
                <h2 id="araguaneycode-title" class="font-display text-[2.25rem] leading-[1.15] font-bold">{{ __('home.araguaneycode.title') }}</h2>
                <p class="text-muted">{{ __('home.araguaneycode.body') }}</p>
            </div>

            <x-button :href="config('site.araguaneycode_url')" rel="noopener" target="_blank" class="flex-none focus-visible:ring-offset-deep">{{ __('home.araguaneycode.cta') }}</x-button>
        </div>
    </section>

    {{-- Contacto --}}
    <section id="{{ $anchors['contact'] }}" class="scroll-mt-20 border-t border-line-subtle">
        <div class="mx-auto flex max-w-site flex-wrap gap-16 px-4 py-24 sm:px-6">
            <div class="flex min-w-0 flex-[1_1_25rem] flex-col items-start gap-5">
                <x-section-heading :eyebrow="$eyebrows['contact']" :title="__('home.contact.title')" />

                <p class="text-muted">{{ __('home.contact.body') }}</p>

                <a href="mailto:{{ config('site.email') }}" class="font-display text-[26px] font-bold text-ice hover:text-mist">{{ config('site.email') }}</a>

                <ul class="flex flex-wrap gap-3">
                    @foreach (array_filter(config('site.social')) as $network => $url)
                        <li>
                            <a href="{{ $url }}" rel="me noopener" target="_blank" class="inline-flex min-h-11 items-center rounded-btn border border-line-strong px-4 text-[15px] text-mist transition-colors hover:border-ice">{{ $network }}</a>
                        </li>
                    @endforeach
                </ul>

                <div class="flex flex-wrap gap-3.5 pt-2">
                    @foreach (__('home.contact.cv') as $cvLocale => $cvLabel)
                        <x-button variant="secondary" href="#" :hreflang="$cvLocale" class="px-5">
                            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 3v12" /><path d="M7 10l5 5 5-5" /><path d="M5 21h14" />
                            </svg>
                            {{ $cvLabel }}
                        </x-button>
                    @endforeach
                </div>
            </div>

            <form class="flex min-w-0 flex-[1_1_27.5rem] flex-col gap-[18px] rounded-card border border-line-dark bg-deep p-8">
                <x-input :label="__('home.contact.form.name')" name="name" autocomplete="name" />
                <x-input :label="__('home.contact.form.email')" name="email" type="email" autocomplete="email" />
                <x-select :label="__('home.contact.form.topic')" name="topic" :options="__('home.contact.form.topics')" />
                <x-input :label="__('home.contact.form.message')" name="message" type="textarea" />
                <x-button type="submit" class="w-full focus-visible:ring-offset-deep">{{ __('home.contact.form.submit') }}</x-button>
            </form>
        </div>
    </section>
</x-layouts::site>
