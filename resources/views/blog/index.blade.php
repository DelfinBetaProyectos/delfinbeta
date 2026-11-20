<x-layouts::site :title="__('blog.meta_title')">
    @php
        $blogUrl = route('blog.index');
        $mediumLink = '<a href="'.e(config('site.social.Medium')).'" rel="me noopener" target="_blank" class="text-ice underline underline-offset-4 hover:text-mist">Medium</a>';
        $filters = ['' => __('blog.all')] + collect($pillars)->map(fn (array $pillar): string => $pillar['filter'])->all();
    @endphp

    {{-- Cabecera --}}
    <section class="mx-auto flex max-w-site flex-col gap-5 px-4 pt-20 pb-10 sm:px-6">
        <p class="font-mono text-sm text-ice">{{ __('blog.eyebrow') }}</p>

        <h1 class="max-w-[860px] font-display text-[clamp(2.5rem,5vw,4rem)] leading-[1.05] font-bold tracking-[-0.03em]">{{ __('blog.title') }}</h1>

        <p class="max-w-[680px] text-muted">{!! str_replace(':medium', $mediumLink, e(__('blog.lead'))) !!}</p>

        <div role="group" aria-label="{{ __('blog.filter_label') }}" class="flex flex-wrap gap-2.5 pt-3">
            @foreach ($filters as $pillarKey => $label)
                @php($isActive = ($activePillar ?? '') === $pillarKey)
                <a
                    href="{{ $pillarKey === '' ? $blogUrl : $blogUrl.'?pillar='.$pillarKey }}"
                    @if ($isActive) aria-current="page" @endif
                    @class([
                        'inline-flex min-h-11 items-center rounded-full border px-[18px] text-[15px] font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ice focus-visible:ring-offset-2 focus-visible:ring-offset-navy',
                        'border-ice bg-ice text-navy' => $isActive,
                        'border-line-strong text-mist hover:border-ice' => ! $isActive,
                    ])
                >{{ $label }}</a>
            @endforeach
        </div>
    </section>

    {{-- Destacado --}}
    @foreach ($featured as $post)
        <section class="mx-auto max-w-site px-4 pt-6 pb-12 sm:px-6">
            <article class="flex flex-wrap overflow-hidden rounded-[20px] border border-line-dark bg-deep">
                <div class="flex min-h-[280px] flex-[1_1_27.5rem] items-center justify-center bg-deep-dark p-8">
                    @if ($post['code'] ?? null)
                        <pre class="overflow-x-auto font-mono text-sm leading-[1.8] text-mist">{{ $post['code'] }}</pre>
                    @endif
                </div>

                <div class="flex min-w-0 flex-[999_1_27.5rem] flex-col gap-3.5 p-8 md:p-10">
                    <p class="font-mono text-[13px] font-medium text-ice">
                        {{ collect([
                            __('blog.featured'),
                            $pillars[$post['pillar']]['label'],
                            $post['reading_time'] ? __('blog.reading_time', ['minutes' => $post['reading_time']]) : null,
                        ])->filter()->join(' · ') }}
                    </p>
                    <h2 class="font-display text-[2.125rem] leading-[1.15] font-bold">{{ $post['title'] }}</h2>
                    <p class="text-muted">{{ $post['summary'] }}</p>
                    <a href="#" class="mt-2 font-medium text-ice underline underline-offset-4 hover:text-mist">
                        {{ __('blog.read_article') }} <span aria-hidden="true">→</span>
                    </a>
                </div>
            </article>
        </section>
    @endforeach

    {{-- Lista --}}
    <section class="bg-mist text-navy">
        <div class="mx-auto flex max-w-site flex-col gap-6 px-4 pt-18 pb-24 sm:px-6">
            <p class="font-mono text-sm font-medium text-deep">{{ trans_choice('blog.count', $posts->count()) }}</p>

            @if ($posts->isNotEmpty())
                <div class="grid grid-cols-[repeat(auto-fill,minmax(min(18.75rem,100%),1fr))] gap-6">
                    @foreach ($posts as $post)
                        <x-post-card :pillar="$pillars[$post['pillar']]['label']" :date="$post['date']" :title="$post['title']" :summary="$post['summary']" href="#" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Sígueme --}}
    <section class="bg-deep">
        <div class="mx-auto flex max-w-site flex-wrap items-center justify-between gap-6 px-4 py-16 sm:px-6">
            <div class="flex max-w-[640px] flex-col gap-2">
                <h2 class="font-display text-[1.75rem] leading-tight font-bold">{{ __('blog.follow.title') }}</h2>
                <p class="text-muted">{{ __('blog.follow.body') }}</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <x-button :href="config('site.social.LinkedIn')" rel="me noopener" target="_blank" class="focus-visible:ring-offset-deep">LinkedIn</x-button>
                <x-button variant="secondary" :href="config('site.social.Instagram')" rel="me noopener" target="_blank" class="focus-visible:ring-offset-deep">Instagram</x-button>
            </div>
        </div>
    </section>
</x-layouts::site>
