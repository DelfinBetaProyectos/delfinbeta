<?php

use App\Enums\ArticleCategory;
use App\Livewire\Forms\ArticleForm;
use App\Models\Article;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

new #[Title('Article')] class extends Component {
    use WithFileUploads;

    public ArticleForm $form;

    /**
     * Load the article being edited, or start a blank draft on the create route.
     */
    public function mount(?Article $article = null): void
    {
        if ($article?->exists) {
            $this->form->setArticle($article);
        } else {
            $this->form->initialize();
        }
    }

    /**
     * Suggest a slug from the Spanish title while creating an article.
     */
    public function updated(string $property, mixed $value): void
    {
        $titleProperty = 'form.title.'.config('site.locales')[0];

        if ($property === $titleProperty && ! $this->form->article && blank($this->form->slug)) {
            $this->form->slug = Str::slug((string) $value);
        }
    }

    /**
     * Save the article; reload the edit page when its URL changed.
     */
    public function save(): void
    {
        $previousSlug = $this->form->article?->slug;

        $article = $this->form->save();

        Flux::toast(variant: 'success', text: __('Article saved.'));

        if ($article->slug !== $previousSlug) {
            $this->redirectRoute('dashboard.articles.edit', $article, navigate: true);
        }
    }
}; ?>

<section class="w-full">
    @php
        $locales = config('site.locales');
        $labels = ArticleForm::labels();
    @endphp

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:breadcrumbs class="mb-2">
                <flux:breadcrumbs.item :href="route('dashboard.articles.index')" wire:navigate>{{ __('Articles') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $form->article ? __('Edit') : __('New') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>

            <flux:heading size="xl" level="1">{{ $form->article ? $form->article->title : __('New article') }}</flux:heading>
        </div>

        @if ($form->article?->isPublished())
            <flux:button variant="ghost" icon="arrow-top-right-on-square" :href="route('blog.show', $form->article)" target="_blank">{{ __('View on site') }}</flux:button>
        @endif
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <form wire:submit="save" class="flex max-w-3xl flex-col gap-10">
        {{-- Datos generales --}}
        <div class="flex flex-col gap-6">
            <flux:heading size="lg" level="2">{{ __('General') }}</flux:heading>

            <div class="grid gap-6 sm:grid-cols-[1fr_14rem]">
                <flux:input wire:model="form.slug" :label="__('Slug')" :description="__('URL: /blog/{slug}. Lowercase letters, numbers and hyphens.')" required />

                <flux:select wire:model="form.category" :label="__('Blog topic')" required>
                    @foreach (ArticleCategory::cases() as $category)
                        <flux:select.option :value="$category->value">{{ $category->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <x-publication-date-field :value="$form->published_on" />

            <flux:switch wire:model="form.is_featured" :label="__('Featured')" :description="__('Highlighted at the top of the blog. Only one article can be featured.')" align="left" />

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input wire:model="form.medium_url" type="url" :label="__('Medium URL')" :badge="__('Optional')" placeholder="https://medium.com/@delfinbeta/..." />
                <flux:input wire:model="form.dev_url" type="url" :label="__('DEV URL')" :badge="__('Optional')" placeholder="https://dev.to/delfinbeta/..." />
            </div>

            <flux:field>
                <flux:label badge="{{ __('Optional') }}">{{ __('Cover image') }}</flux:label>
                <flux:description>{{ __('JPG, PNG or WebP up to 4 MB, ideally landscape (about 1600 × 900). Shown at the top of the article and on the blog when it is featured.') }}</flux:description>

                @if ($form->image instanceof TemporaryUploadedFile && $form->image->isPreviewable())
                    <img src="{{ $form->image->temporaryUrl() }}" alt="" class="aspect-video max-h-48 w-fit rounded-lg border border-zinc-700 object-cover">
                @elseif ($form->article?->image && ! $form->remove_image)
                    <img src="{{ $form->article->imageUrl() }}" alt="" class="aspect-video max-h-48 w-fit rounded-lg border border-zinc-700 object-cover">
                @endif

                <flux:input type="file" wire:model="form.image" accept="image/*" />
                <flux:error name="form.image" />
            </flux:field>

            @if ($form->article?->image)
                <flux:checkbox wire:model.live="form.remove_image" :label="__('Remove current cover image')" />
            @endif
        </div>

        {{-- Contenido por idioma --}}
        <div x-data="{ locale: @js($locales[0]) }" class="flex flex-col gap-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <flux:heading size="lg" level="2">{{ __('Content') }}</flux:heading>

                <div role="tablist" aria-label="{{ __('Language') }}" class="flex gap-1 rounded-lg bg-zinc-800 p-1">
                    @foreach ($locales as $locale)
                        <button
                            type="button"
                            role="tab"
                            wire:key="locale-tab-{{ $locale }}"
                            x-on:click="locale = @js($locale)"
                            x-bind:aria-selected="locale === @js($locale)"
                            x-bind:class="locale === @js($locale) ? 'bg-zinc-600 text-white' : 'text-zinc-400 hover:text-white'"
                            class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium"
                        >
                            {{ config("site.locale_names.{$locale}") }}
                            @if ($errors->get("form.*.{$locale}"))
                                <span class="size-2 rounded-full bg-red-500" aria-label="{{ __('Has errors') }}"></span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            @foreach ($locales as $locale)
                <div wire:key="locale-fields-{{ $locale }}" x-show="locale === @js($locale)" role="tabpanel" lang="{{ $locale }}" class="flex flex-col gap-6">
                    <flux:input wire:model.blur="form.title.{{ $locale }}" :label="$labels['title']" />
                    <flux:textarea wire:model="form.summary.{{ $locale }}" :label="$labels['summary']" :description="__('2 or 3 lines; shown on the cards and under the title.')" rows="3" />
                    <flux:textarea wire:model="form.body.{{ $locale }}" :label="$labels['body']" :description="__('Markdown: ## headings, **bold**, [links](https://…), lists and ``` code blocks.')" rows="18" class="font-mono" />
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            <flux:button variant="ghost" :href="route('dashboard.articles.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </form>
</section>
