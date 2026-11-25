<x-layouts::site :title="__('home.meta_title')">
    @php
        $anchors = collect(__('nav.sections'))->map(fn (array $section): string => $section['anchor']);
        $showContent = $articles->count() >= App\Models\Article::HOME_MINIMUM;
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
            <img src="{{ asset('images/Dayan.jpg') }}" alt="{{ __('home.hero.photo') }}" width="960" height="1200" fetchpriority="high" class="h-auto w-full rounded-[20px] border-2 border-sea object-cover">

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
                <x-section-heading on="light" :eyebrow="$eyebrows['story']">
                    <x-slot:title><x-years-of-experience :text="__('home.story.title')" /></x-slot:title>
                </x-section-heading>

                @foreach (__('home.story.body') as $paragraph)
                    <p class="text-ink-strong">{{ $paragraph }}</p>
                @endforeach

                <a href="{{ route('story') }}" class="font-medium text-deep underline underline-offset-4 hover:no-underline">
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
                <x-project-card :label="$project->label" :title="$project->title" :summary="$project->summary" :image="$project->image" :href="route('projects.show', $project)" />
            @endforeach
        </div>

        @if ($hasMoreProjects)
            <a href="{{ route('projects.index') }}" class="self-start font-medium text-ice underline underline-offset-4 hover:text-mist">
                {{ __('home.projects.view_all') }} <span aria-hidden="true">→</span>
            </a>
        @endif
    </section>

    {{-- Comunidad y charlas --}}
    <section id="{{ $anchors['community'] }}" class="scroll-mt-20 bg-deep">
        <div class="mx-auto flex max-w-site flex-wrap gap-14 px-4 py-24 sm:px-6">
            <div class="flex min-w-0 flex-[1_1_25rem] flex-col gap-5">
                <x-section-heading :eyebrow="$eyebrows['community']" :title="__('home.community.title')" />
                <p class="text-muted">{{ __('home.community.body') }}</p>

                <div class="pt-2">
                    <x-button :href="'#'.$anchors['contact']" class="focus-visible:ring-offset-deep">{{ __('home.community.cta') }}</x-button>
                </div>
            </div>

            <div class="grid min-w-0 flex-[1_1_27.5rem] grid-cols-[696fr_872fr] items-start gap-4 self-center">
                <img src="{{ asset('images/StartupWeekend.jpg') }}" alt="{{ __('home.community.photos.startup_weekend') }}" width="696" height="873" loading="lazy" class="h-auto w-full rounded-card border border-line-dark">
                <img src="{{ asset('images/HFMaracay.jpg') }}" alt="{{ __('home.community.photos.hf_maracay') }}" width="872" height="873" loading="lazy" class="h-auto w-full rounded-card border border-line-dark">
            </div>
        </div>
    </section>

    {{-- Contenido: solo con 3 o más artículos publicados --}}
    @if ($showContent)
        <section id="{{ $anchors['content'] }}" class="scroll-mt-20 bg-mist text-navy">
            <div class="mx-auto flex max-w-site flex-col gap-10 px-4 py-24 sm:px-6">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading on="light" :eyebrow="$eyebrows['content']" :title="__('home.content.title')" />
                    <a href="{{ route('blog.index') }}" class="font-medium text-deep underline underline-offset-4 hover:no-underline">
                        {{ __('home.content.view_all') }} <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(min(18.75rem,100%),1fr))] gap-6">
                    @foreach ($articles as $article)
                        <x-post-card :category="$article->category->label()" :date="$article->published_at" :title="$article->title" :summary="$article->summary" :href="route('blog.show', $article)" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- AraguaneyCode --}}
    <section aria-labelledby="araguaneycode-title" class="mx-auto max-w-site px-4 py-24 sm:px-6">
        <div class="flex flex-wrap items-center gap-12 rounded-3xl border border-line-dark bg-deep p-8 md:p-14">
            <img src="{{ asset('images/AraguaneyCode.jpg') }}" alt="AraguaneyCode" width="160" height="160" loading="lazy" class="size-40 flex-none rounded-xl border border-line-dark">

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

                @if (config('site.whatsapp'))
                    <x-button :href="'https://wa.me/'.config('site.whatsapp').'?text='.rawurlencode(__('home.contact.whatsapp.message'))" rel="noopener" target="_blank">
                        <svg class="size-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35zM12.05 21.5h-.01a9.45 9.45 0 0 1-4.82-1.32l-.35-.21-3.58.94.96-3.49-.23-.36a9.43 9.43 0 0 1-1.45-5.03c0-5.22 4.25-9.47 9.48-9.47a9.4 9.4 0 0 1 6.7 2.78 9.4 9.4 0 0 1 2.77 6.7c0 5.22-4.25 9.46-9.47 9.46zm8.06-17.53A11.32 11.32 0 0 0 12.05.63C5.77.63.66 5.74.66 12.02c0 2 .52 3.96 1.52 5.69L.57 23.6l6.04-1.58a11.38 11.38 0 0 0 5.44 1.38h.01c6.28 0 11.39-5.11 11.39-11.39 0-3.04-1.18-5.9-3.34-8.05z" />
                        </svg>
                        {{ __('home.contact.whatsapp.cta') }}
                    </x-button>
                @endif

                <ul class="flex flex-wrap gap-3">
                    @foreach (array_filter(config('site.social')) as $network => $url)
                        <li>
                            <a href="{{ $url }}" rel="me noopener" target="_blank" class="inline-flex min-h-11 items-center rounded-btn border border-line-strong px-4 text-[15px] text-mist transition-colors hover:border-ice">{{ $network }}</a>
                        </li>
                    @endforeach
                </ul>

                <div class="flex flex-wrap gap-3.5 pt-2">
                    @foreach (__('home.contact.cv') as $cvLocale => $cvLabel)
                        <x-button variant="secondary" :href="route('resume.download', $cvLocale)" download :hreflang="$cvLocale" class="px-5">
                            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 3v12" /><path d="M7 10l5 5 5-5" /><path d="M5 21h14" />
                            </svg>
                            {{ $cvLabel }}
                        </x-button>
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="flex min-w-0 flex-[1_1_27.5rem] flex-col gap-[18px] rounded-card border border-line-dark bg-deep p-8">
                @csrf

                @if (session('contact_sent'))
                    <div role="status" class="flex flex-col gap-1 rounded-btn border border-ice/60 bg-navy px-4 py-3.5">
                        <p class="font-medium text-white">{{ __('home.contact.form.sent_title') }}</p>
                        <p class="text-[15px] text-muted">{{ __('home.contact.form.sent_body', ['hours' => config('site.reply_within_hours')]) }}</p>
                    </div>
                @endif

                <x-input :label="__('home.contact.form.name')" name="name" autocomplete="name" required maxlength="120" />
                <x-input :label="__('home.contact.form.email')" name="email" type="email" autocomplete="email" required maxlength="255" />
                <x-select :label="__('home.contact.form.topic')" name="topic" :options="__('home.contact.form.topics')" required />
                <x-input :label="__('home.contact.form.message')" name="message" type="textarea" required minlength="10" maxlength="5000" />

                {{-- Honeypot: hidden from people, filled in by bots. Inline styles so it stays hidden even without the compiled CSS. --}}
                <div style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;" aria-hidden="true">
                    <label for="{{ App\Http\Requests\StoreContactMessageRequest::HONEYPOT_FIELD }}">{{ __('home.contact.form.honeypot') }}</label>
                    <input type="text" name="{{ App\Http\Requests\StoreContactMessageRequest::HONEYPOT_FIELD }}" id="{{ App\Http\Requests\StoreContactMessageRequest::HONEYPOT_FIELD }}" value="" tabindex="-1" autocomplete="off">
                </div>

                <x-button type="submit" class="w-full focus-visible:ring-offset-deep">{{ __('home.contact.form.submit') }}</x-button>
            </form>
        </div>
    </section>
</x-layouts::site>
