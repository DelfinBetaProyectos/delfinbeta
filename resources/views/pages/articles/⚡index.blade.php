<?php

use App\Models\Article;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Articles')] class extends Component {
    public ?int $deletingArticleId = null;

    /**
     * Every article: drafts first, then newest publication date first.
     *
     * @return Collection<int, Article>
     */
    #[Computed]
    public function articles(): Collection
    {
        return Article::query()->orderByRaw('published_at is not null')->orderByDesc('published_at')->orderByDesc('id')->get();
    }

    /**
     * How many articles are visible on the site, and whether the blog and the home section show.
     *
     * @return array{published: int, blogVisible: bool, homeVisible: bool}
     */
    #[Computed]
    public function visibility(): array
    {
        $published = Article::query()->published()->count();

        return [
            'published' => $published,
            'blogVisible' => $published > 0,
            'homeVisible' => $published >= Article::HOME_MINIMUM,
        ];
    }

    /**
     * Publish an article today, or send it back to draft. A scheduled article is published today.
     */
    public function togglePublished(int $articleId): void
    {
        $article = Article::query()->findOrFail($articleId);

        if ($article->isPublished()) {
            $article->unpublish();
        } else {
            $article->publishToday();
        }

        unset($this->articles, $this->visibility);

        Flux::toast(variant: 'success', text: $article->isPublished() ? __('Article published.') : __('Article moved to drafts.'));
    }

    /**
     * Ask for confirmation before deleting an article.
     */
    public function confirmDelete(int $articleId): void
    {
        $this->deletingArticleId = Article::query()->findOrFail($articleId)->id;

        Flux::modal('delete-article')->show();
    }

    /**
     * Delete the article awaiting confirmation.
     */
    public function delete(): void
    {
        Article::query()->findOrFail($this->deletingArticleId)->delete();

        $this->deletingArticleId = null;
        unset($this->articles, $this->visibility);

        Flux::modal('delete-article')->close();
        Flux::toast(variant: 'success', text: __('Article deleted.'));
    }

    /**
     * A publication date in Maracay time and the dashboard language.
     */
    public function publicationDate(Article $article): string
    {
        return $article->published_at->timezone(config('site.timezone'))->locale(app()->getLocale())->isoFormat('D MMM YYYY');
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('Articles') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Blog articles, shown on the site once published') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" :href="route('dashboard.articles.create')" wire:navigate>{{ __('New article') }}</flux:button>
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <flux:callout :icon="$this->visibility['blogVisible'] ? 'eye' : 'eye-slash'" class="mb-6">
        <flux:callout.heading>{{ trans_choice('{0} No published articles|{1} :count published article|[2,*] :count published articles', $this->visibility['published']) }}</flux:callout.heading>
        <flux:callout.text>
            {{ $this->visibility['blogVisible'] ? __('The blog is visible in the menu.') : __('The blog stays hidden until you publish an article.') }}
            {{ $this->visibility['homeVisible'] ? __('The home page shows the latest articles.') : __('The home page shows the latest articles once :count are published.', ['count' => Article::HOME_MINIMUM]) }}
        </flux:callout.text>
    </flux:callout>

    @if ($this->articles->isEmpty())
        <flux:text>{{ __('There are no articles yet.') }}</flux:text>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Blog topic') }}</flux:table.column>
                <flux:table.column>{{ __('Published') }}</flux:table.column>
                <flux:table.column><span class="sr-only">{{ __('Actions') }}</span></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->articles as $article)
                    <flux:table.row :key="$article->id">
                        <flux:table.cell variant="strong" class="max-w-md">
                            <div class="truncate">
                                <a href="{{ route('dashboard.articles.edit', $article) }}" class="hover:underline" wire:navigate>{{ $article->title }}</a>
                                @if ($article->is_featured)
                                    <flux:icon.star variant="micro" class="ms-1 inline text-amber-400" />
                                    <span class="sr-only">{{ __('Featured') }}</span>
                                @endif
                            </div>
                            <div class="truncate font-mono text-xs font-normal text-zinc-500 dark:text-zinc-400">{{ $article->slug }}</div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $article->category->label() }}</flux:table.cell>

                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                <flux:switch
                                    wire:key="publish-article-{{ $article->id }}-{{ $article->published_at?->timestamp }}"
                                    :checked="$article->isPublished()"
                                    wire:change="togglePublished({{ $article->id }})"
                                    :aria-label="__('Published').': '.$article->title"
                                />

                                <span class="text-xs whitespace-nowrap">
                                    @if ($article->isPublished())
                                        {{ $this->publicationDate($article) }}
                                    @elseif ($article->isScheduled())
                                        <span class="text-sky-500 dark:text-sky-400">{{ __('Scheduled') }} · {{ $this->publicationDate($article) }}</span>
                                    @else
                                        {{ __('Draft') }}
                                    @endif
                                </span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown position="bottom" align="end">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" :aria-label="__('Actions')" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" :href="route('dashboard.articles.edit', $article)" wire:navigate>{{ __('Edit') }}</flux:menu.item>

                                    @if ($article->isPublished())
                                        <flux:menu.item icon="arrow-top-right-on-square" :href="route('blog.show', $article)" target="_blank">{{ __('View on site') }}</flux:menu.item>
                                    @endif

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $article->id }})">{{ __('Delete') }}</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif

    <flux:modal name="delete-article" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete article?') }}</flux:heading>
                <flux:text class="mt-2">{{ __('The article will be permanently deleted. This cannot be undone.') }}</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
