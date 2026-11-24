<x-layouts::site :title="__('projects.meta_title')">
    {{-- Cabecera --}}
    <section class="mx-auto flex max-w-site flex-col gap-5 px-4 pt-20 pb-10 sm:px-6">
        <p class="font-mono text-sm text-ice">{{ __('projects.eyebrow') }}</p>

        <h1 class="max-w-[860px] font-display text-[clamp(2.5rem,5vw,4rem)] leading-[1.05] font-bold tracking-[-0.03em]">{{ __('projects.title') }}</h1>

        <p class="max-w-[680px] text-muted">{{ __('projects.lead') }}</p>
    </section>

    {{-- Proyectos --}}
    <section class="mx-auto max-w-site px-4 pt-6 pb-24 sm:px-6">
        <div class="grid grid-cols-[repeat(auto-fill,minmax(min(20rem,100%),1fr))] gap-6">
            @foreach ($projects as $project)
                <x-project-card :label="$project->label" :title="$project->title" :summary="$project->summary" :image="$project->image" :href="route('projects.show', $project)" />
            @endforeach
        </div>
    </section>

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
