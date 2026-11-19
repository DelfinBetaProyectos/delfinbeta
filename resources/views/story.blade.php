<x-layouts::site :title="__('story.meta_title')">
    @php
        $homeUrl = URL::localizedRoute('home');
    @endphp

    {{-- Intro --}}
    <section class="mx-auto flex max-w-site flex-wrap items-center gap-14 px-4 pt-16 pb-16 sm:px-6 md:pt-22">
        <div class="flex min-w-0 flex-[999_1_32rem] flex-col gap-[22px]">
            <p class="font-mono text-sm text-ice">{{ __('story.intro.eyebrow') }}</p>

            <h1 class="font-display text-[clamp(2.5rem,5vw,4rem)] leading-[1.05] font-bold tracking-[-0.03em]">
                <x-years-of-experience :text="__('story.intro.title')" />
            </h1>

            <p class="text-[19px] text-muted">{{ __('story.intro.lead') }}</p>
        </div>

        <x-photo-placeholder variant="deep" aspect="1/1" :label="__('story.intro.photo')" class="min-w-0 flex-[1_1_21.25rem]" />
    </section>

    {{-- Trayectoria --}}
    <section class="bg-mist text-navy">
        <div class="mx-auto flex max-w-[60rem] flex-col gap-10 px-4 py-24 sm:px-6">
            <x-section-heading on="light" size="md" :eyebrow="__('story.career.eyebrow')" :title="__('story.career.title')" />

            <ol>
                @foreach (__('story.career.items') as $item)
                    <x-career-item :period="$item['period']" :title="$item['title']">
                        {{ $item['body'] }}
                        @isset($item['link'])
                            <a href="{{ config('site.araguaneycode_url') }}" rel="noopener" target="_blank" class="font-medium text-deep underline underline-offset-2 hover:no-underline">{{ $item['link'] }}</a>
                        @endisset
                    </x-career-item>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- #MamaDeveloper --}}
    <section class="mx-auto flex max-w-site flex-wrap items-center gap-14 px-4 py-24 sm:px-6">
        <x-photo-placeholder variant="deep" aspect="4/3" :label="__('story.mama_developer.photo')" class="min-w-0 flex-[1_1_25rem]" />

        <div class="flex min-w-0 flex-[999_1_27.5rem] flex-col gap-5">
            <x-section-heading size="md" :eyebrow="__('story.mama_developer.eyebrow')" :title="__('story.mama_developer.title')" class="max-w-none" />

            <p class="text-muted">{{ __('story.mama_developer.body') }}</p>

            <a href="{{ URL::localizedRoute('blog.index').'?pillar=mama' }}" class="font-medium text-ice underline underline-offset-4 hover:text-mist">
                {{ __('story.mama_developer.link') }} <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-deep">
        <div class="mx-auto flex max-w-site flex-wrap items-center justify-between gap-6 px-4 py-18 sm:px-6">
            <h2 class="max-w-[620px] font-display text-[2rem] leading-tight font-bold">{{ __('story.cta.title') }}</h2>

            <div class="flex flex-wrap gap-3.5">
                <x-button :href="$homeUrl.'#'.__('nav.sections.projects.anchor')" class="focus-visible:ring-offset-deep">{{ __('story.cta.projects') }}</x-button>
                <x-button variant="secondary" :href="$homeUrl.'#'.__('nav.sections.contact.anchor')" class="focus-visible:ring-offset-deep">{{ __('story.cta.cv') }}</x-button>
            </div>
        </div>
    </section>
</x-layouts::site>
