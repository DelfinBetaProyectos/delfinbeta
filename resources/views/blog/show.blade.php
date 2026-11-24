<x-layouts::site :title="$article->title" :description="$article->summary" :image="$article->imageUrl()">
    @php
        $blogUrl = route('blog.index');
        $publishedAt = $article->published_at;
    @endphp

    {{-- Cabecera del artículo --}}
    <section class="mx-auto flex max-w-site flex-col gap-6 px-4 pt-16 pb-12 sm:px-6">
        <nav aria-label="{{ __('blog.breadcrumb_label') }}">
            <ol class="flex flex-wrap gap-2 font-mono text-sm text-muted">
                <li><a href="{{ $blogUrl }}" class="underline underline-offset-4 hover:text-mist">{{ __('blog.eyebrow') }}</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ $blogUrl.'?category='.$article->category->value }}" class="underline underline-offset-4 hover:text-mist">{{ $article->category->label() }}</a></li>
            </ol>
        </nav>

        <h1 class="max-w-[900px] font-display text-[clamp(2.25rem,5vw,3.75rem)] leading-[1.08] font-bold tracking-[-0.03em]">{{ $article->title }}</h1>

        <p class="max-w-[760px] text-[19px] text-muted">{{ $article->summary }}</p>

        <p class="font-mono text-[13px] font-medium text-ice">
            <time datetime="{{ $publishedAt->toDateString() }}">{{ $publishedAt->translatedFormat('j M Y') }}</time>
            <span aria-hidden="true">·</span>
            {{ __('blog.reading_time', ['minutes' => $article->readingTime()]) }}
        </p>
    </section>

    {{-- Portada --}}
    @if ($article->image)
        <section class="mx-auto max-w-site px-4 pb-14 sm:px-6">
            <img src="{{ $article->imageUrl() }}" alt="" class="mx-auto aspect-video h-auto w-full max-w-5xl rounded-[20px] border border-line-dark object-cover">
        </section>
    @endif

    {{-- Cuerpo --}}
    <section class="bg-mist text-navy">
        <div class="mx-auto max-w-site px-4 py-18 sm:px-6">
            <div @class([
                'mx-auto max-w-[720px] text-[18px] leading-[1.75] text-ink-strong',
                '[&_h2]:mt-12 [&_h2]:mb-4 [&_h2]:font-display [&_h2]:text-[1.875rem] [&_h2]:leading-tight [&_h2]:font-bold [&_h2]:text-navy',
                '[&_h3]:mt-9 [&_h3]:mb-3 [&_h3]:font-display [&_h3]:text-[1.375rem] [&_h3]:font-bold [&_h3]:text-navy',
                '[&_p]:my-5 [&_ul]:my-5 [&_ul]:list-disc [&_ul]:ps-6 [&_ol]:my-5 [&_ol]:list-decimal [&_ol]:ps-6 [&_li]:my-1.5',
                '[&_a]:font-medium [&_a]:text-deep [&_a]:underline [&_a]:underline-offset-4 [&_a:hover]:no-underline',
                '[&_strong]:font-semibold [&_strong]:text-navy',
                '[&_blockquote]:my-6 [&_blockquote]:border-s-4 [&_blockquote]:border-sea [&_blockquote]:ps-5 [&_blockquote]:text-ink',
                '[&_code]:rounded [&_code]:bg-white [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:font-mono [&_code]:text-[0.875em] [&_code]:text-deep',
                '[&_pre]:my-6 [&_pre]:overflow-x-auto [&_pre]:rounded-card [&_pre]:bg-navy [&_pre]:p-5 [&_pre]:text-[15px] [&_pre]:leading-[1.7]',
                '[&_pre_code]:bg-transparent [&_pre_code]:p-0 [&_pre_code]:text-mist',
                '[&_img]:my-6 [&_img]:rounded-card [&_hr]:my-10 [&_hr]:border-line-light',
                '[&>*:first-child]:mt-0',
            ])>
                {!! $article->bodyHtml() !!}
            </div>

            @if ($article->alsoPublishedOn())
                @php
                    $platformLinks = collect($article->alsoPublishedOn())
                        ->map(fn (string $url, string $network): string => '<a href="'.e($url).'" rel="noopener" target="_blank" class="font-medium text-deep underline underline-offset-4 hover:no-underline">'.$network.'</a>')
                        ->join(', ', ' '.__('blog.and').' ');
                @endphp
                <p class="mx-auto mt-12 max-w-[720px] border-t border-line-light pt-6 text-[15px] text-ink">
                    {!! __('blog.also_published', ['platforms' => $platformLinks]) !!}
                </p>
            @endif
        </div>
    </section>

    {{-- Más del mismo tema --}}
    @if ($related->isNotEmpty())
        <section class="mx-auto flex max-w-site flex-col gap-8 px-4 py-20 sm:px-6">
            <h2 class="font-display text-[1.75rem] leading-tight font-bold">{{ __('blog.related', ['category' => $article->category->label()]) }}</h2>

            <div class="grid grid-cols-[repeat(auto-fill,minmax(min(18.75rem,100%),1fr))] gap-6">
                @foreach ($related as $relatedArticle)
                    <x-post-card :category="$relatedArticle->category->label()" :date="$relatedArticle->published_at" :title="$relatedArticle->title" :summary="$relatedArticle->summary" :href="route('blog.show', $relatedArticle)" />
                @endforeach
            </div>
        </section>
    @endif

    <section @class(['mx-auto max-w-site px-4 pb-20 sm:px-6', 'pt-20' => $related->isEmpty()])>
        <a href="{{ $blogUrl }}" class="font-medium text-ice underline underline-offset-4 hover:text-mist">
            <span aria-hidden="true">←</span> {{ __('blog.back') }}
        </a>
    </section>
</x-layouts::site>
