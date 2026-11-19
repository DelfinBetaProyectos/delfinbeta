<x-layouts::site :title="$project['title']">
    @php
        $case = $project['case'];
        $sections = __('project.sections');
        $projectsUrl = URL::localizedRoute('home').'#'.__('nav.sections.projects.anchor');
        $meta = array_filter([
            'role' => $case['role'],
            'period' => $case['period'],
            'stack' => $case['stack'],
        ]);
    @endphp

    {{-- Cabecera del caso --}}
    <section class="mx-auto flex max-w-site flex-col gap-6 px-4 pt-16 pb-12 sm:px-6">
        <nav aria-label="{{ __('project.breadcrumb_label') }}">
            <ol class="flex flex-wrap gap-2 font-mono text-sm text-muted">
                <li><a href="{{ $projectsUrl }}" class="underline underline-offset-4 hover:text-mist">{{ __('project.breadcrumb') }}</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" class="text-ice">{{ $case['breadcrumb'] }}</li>
            </ol>
        </nav>

        <h1 class="max-w-[900px] font-display text-[clamp(2.5rem,5vw,4rem)] leading-[1.05] font-bold tracking-[-0.03em]">{{ $case['title'] }}</h1>

        <p class="max-w-[760px] text-[19px] text-muted">{{ $case['lead'] }}</p>

        <dl class="mt-4 grid grid-cols-[repeat(auto-fit,minmax(12.5rem,1fr))] gap-6 border-y border-line-subtle py-7">
            @foreach ($meta as $key => $value)
                <div>
                    <dt class="font-mono text-[13px] text-ice">{{ __("project.meta.{$key}") }}</dt>
                    <dd class="mt-1">{{ $value }}</dd>
                </div>
            @endforeach

            @if ($case['live_url'])
                <div>
                    <dt class="font-mono text-[13px] text-ice">{{ __('project.meta.live') }}</dt>
                    <dd class="mt-1">
                        <a href="{{ $case['live_url'] }}" rel="noopener" target="_blank" class="text-ice underline underline-offset-4 hover:text-mist">{{ parse_url($case['live_url'], PHP_URL_HOST) }}</a>
                    </dd>
                </div>
            @endif
        </dl>
    </section>

    {{-- Captura principal --}}
    <section class="mx-auto max-w-site px-4 pb-18 sm:px-6">
        <x-photo-placeholder variant="deep" aspect="2/1" :label="$case['capture']" />
    </section>

    {{-- Cuerpo del caso --}}
    <section class="bg-mist text-navy">
        <div class="mx-auto flex max-w-site flex-wrap gap-16 px-4 py-24 sm:px-6">
            <aside class="min-w-0 flex-[1_1_13.75rem]">
                <nav aria-label="{{ __('project.toc_label') }}" class="lg:sticky lg:top-24">
                    <ol class="flex flex-col gap-2.5 font-mono text-sm">
                        @foreach (array_values($sections) as $index => $section)
                            <li>
                                <a href="#{{ $section['anchor'] }}" class="text-deep underline underline-offset-4 hover:no-underline">{{ sprintf('%02d', $index + 1) }} {{ $section['title'] }}</a>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </aside>

            <div class="flex max-w-[720px] min-w-0 flex-[999_1_35rem] flex-col gap-14">
                <div id="{{ $sections['context']['anchor'] }}" class="flex scroll-mt-24 flex-col gap-3.5">
                    <h2 class="font-display text-[2rem] font-bold">{{ $sections['context']['title'] }}</h2>
                    <p class="text-ink-strong">{{ $case['context'] }}</p>
                </div>

                <div id="{{ $sections['problem']['anchor'] }}" class="flex scroll-mt-24 flex-col gap-3.5">
                    <h2 class="font-display text-[2rem] font-bold">{{ $sections['problem']['title'] }}</h2>
                    <p class="text-ink-strong">{{ $case['problem'] }}</p>
                </div>

                <div id="{{ $sections['built']['anchor'] }}" class="flex scroll-mt-24 flex-col gap-3.5">
                    <h2 class="font-display text-[2rem] font-bold">{{ $sections['built']['title'] }}</h2>

                    <ul class="flex list-disc flex-col gap-2 pl-[22px] text-ink-strong">
                        @foreach ($case['built'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    @if ($case['built_captures'])
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            @foreach ($case['built_captures'] as $capture)
                                <x-photo-placeholder :label="$capture" />
                            @endforeach
                        </div>
                    @endif
                </div>

                <div id="{{ $sections['result']['anchor'] }}" class="flex scroll-mt-24 flex-col gap-5">
                    <h2 class="font-display text-[2rem] font-bold">{{ $sections['result']['title'] }}</h2>

                    @if ($case['stats'])
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            @foreach ($case['stats'] as $stat)
                                <x-stat-card :value="$stat['value']" :label="$stat['label']" />
                            @endforeach
                        </div>
                    @endif

                    <p class="text-ink-strong">{{ $case['result'] }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Proyecto anterior / siguiente --}}
    <nav aria-label="{{ __('project.more_projects') }}" class="mx-auto flex max-w-site flex-wrap gap-6 px-4 py-18 sm:px-6">
        <x-pager-link
            direction="previous"
            :href="URL::localizedRoute('projects.show', ['project' => $previous['slug']])"
            :label="__('project.previous')"
            :title="$previous['title']"
        />
        <x-pager-link
            direction="next"
            :href="URL::localizedRoute('projects.show', ['project' => $next['slug']])"
            :label="__('project.next')"
            :title="$next['title']"
        />
    </nav>

    {{-- CTA AraguaneyCode --}}
    <section class="bg-deep">
        <div class="mx-auto flex max-w-site flex-wrap items-center justify-between gap-6 px-4 py-16 sm:px-6">
            <div class="flex max-w-[640px] flex-col gap-2">
                <h2 class="font-display text-[1.75rem] leading-tight font-bold">{{ __('project.cta.title') }}</h2>
                <p class="text-muted">{{ __('project.cta.body') }}</p>
            </div>

            <x-button :href="config('site.araguaneycode_url')" rel="noopener" target="_blank" class="focus-visible:ring-offset-deep">{{ __('project.cta.button') }}</x-button>
        </div>
    </section>
</x-layouts::site>
