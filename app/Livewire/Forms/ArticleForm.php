<?php

namespace App\Livewire\Forms;

use App\Enums\ArticleCategory;
use App\Models\Article;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;
use RuntimeException;

/**
 * Dashboard form for a blog article. Translatable fields are arrays keyed by locale.
 *
 * The publication date is picked as a day in Maracay time: empty keeps the article as a draft,
 * today or earlier publishes it, and a future day schedules it.
 */
class ArticleForm extends Form
{
    /**
     * Translatable fields, all required in every locale.
     *
     * @var list<string>
     */
    public const TRANSLATABLE_FIELDS = ['title', 'summary', 'body'];

    public ?Article $article = null;

    public string $slug = '';

    public string $category = '';

    /**
     * Publication day as "Y-m-d" in the site timezone, or empty for a draft.
     */
    public string $published_on = '';

    public bool $is_featured = false;

    public string $medium_url = '';

    public string $dev_url = '';

    /** @var UploadedFile|null New cover image to store. */
    public $image = null;

    public bool $remove_image = false;

    /** @var array<string, string> */
    public array $title = [];

    /** @var array<string, string> */
    public array $summary = [];

    /** @var array<string, string> Markdown. */
    public array $body = [];

    /**
     * Start an empty draft with a blank value for every locale.
     */
    public function initialize(): void
    {
        foreach (self::TRANSLATABLE_FIELDS as $field) {
            $this->{$field} = array_fill_keys(config('site.locales'), '');
        }

        $this->category = ArticleCategory::Tech->value;
    }

    /**
     * Fill the form from an existing article.
     */
    public function setArticle(Article $article): void
    {
        $this->article = $article;
        $this->slug = $article->slug;
        $this->category = $article->category->value;
        $this->published_on = $article->published_at?->toDateString() ?? '';
        $this->is_featured = $article->is_featured;
        $this->medium_url = $article->medium_url ?? '';
        $this->dev_url = $article->dev_url ?? '';

        foreach (config('site.locales') as $locale) {
            foreach (self::TRANSLATABLE_FIELDS as $field) {
                $this->{$field}[$locale] = (string) ($article->getTranslations($field)[$locale] ?? '');
            }
        }
    }

    /**
     * Validate and persist the article, storing or removing its cover image.
     * Featuring it un-features every other article.
     */
    public function save(): Article
    {
        $this->validate();

        $article = $this->article ?? new Article;
        $previousImage = $article->image;

        $article->fill([
            'slug' => $this->slug,
            'category' => ArticleCategory::from($this->category),
            'published_at' => $this->publishedAt(),
            'is_featured' => $this->is_featured,
            'medium_url' => filled($this->medium_url) ? $this->medium_url : null,
            'dev_url' => filled($this->dev_url) ? $this->dev_url : null,
            ...collect(self::TRANSLATABLE_FIELDS)->mapWithKeys(fn (string $field): array => [
                $field => array_map(fn (?string $value): string => trim((string) $value), $this->{$field}),
            ])->all(),
        ]);

        if ($this->image) {
            $article->image = $this->storeImage($this->image);
        } elseif ($this->remove_image) {
            $article->image = null;
        }

        $article->save();

        if ($previousImage && $previousImage !== $article->image) {
            Storage::disk('public')->delete($previousImage);
        }

        if ($article->is_featured) {
            Article::query()->whereKeyNot($article->getKey())->where('is_featured', true)->update(['is_featured' => false]);
        }

        $this->article = $article;
        $this->image = null;
        $this->remove_image = false;

        return $article;
    }

    /**
     * Get the validation rules.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = [
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('articles', 'slug')->ignore($this->article)],
            'category' => ['required', Rule::enum(ArticleCategory::class)],
            'published_on' => ['nullable', 'date_format:Y-m-d'],
            'is_featured' => ['boolean'],
            'medium_url' => ['nullable', 'url:https,http', 'max:255'],
            'dev_url' => ['nullable', 'url:https,http', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
            'remove_image' => ['boolean'],
        ];

        foreach (config('site.locales') as $locale) {
            $rules["title.{$locale}"] = ['required', 'string', 'max:255'];
            $rules["summary.{$locale}"] = ['required', 'string', 'max:500'];
            $rules["body.{$locale}"] = ['required', 'string', 'max:100000'];
        }

        return $rules;
    }

    /**
     * Get readable names for the per-locale fields, e.g. "Title (ES)".
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        $attributes = [
            'published_on' => __('Publication date'),
            'medium_url' => __('Medium URL'),
            'dev_url' => __('DEV URL'),
            'image' => __('Cover image'),
            'category' => __('Blog topic'),
        ];

        foreach (config('site.locales') as $locale) {
            foreach (self::labels() as $field => $label) {
                $attributes["{$field}.{$locale}"] = $label.' ('.Str::upper($locale).')';
            }
        }

        return $attributes;
    }

    /**
     * Get the translated label of every translatable field, in form order.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'title' => __('Title'),
            'summary' => __('Summary'),
            'body' => __('Article'),
        ];
    }

    /**
     * Store an uploaded cover image on the public disk and return its path.
     */
    private function storeImage(UploadedFile $file): string
    {
        $path = $file->store('articles', 'public');

        if ($path === false) {
            throw new RuntimeException('The article cover image could not be stored.');
        }

        return $path;
    }

    /**
     * The start of the chosen day in Maracay; null for a draft.
     */
    private function publishedAt(): ?CarbonInterface
    {
        return blank($this->published_on) ? null : Article::startOfDayInSiteTimezone($this->published_on);
    }
}
