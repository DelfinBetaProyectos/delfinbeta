<?php

namespace App\Models;

use App\Enums\ArticleCategory;
use App\Models\Concerns\HasPublicationDate;
use App\Models\Concerns\HasTranslations;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A blog article at /blog/{slug}. Attributes in `$translatable` are stored per locale and read in the current one.
 *
 * Drafts have no `published_at`; a future date keeps the article hidden until then. The blog (nav link and
 * /blog) only exists while at least one article is published, and the home "Contenido" section needs three.
 *
 * @property int $id
 * @property string $slug
 * @property ArticleCategory $category
 * @property Carbon|null $published_at
 * @property bool $is_featured
 * @property string $title
 * @property string $summary
 * @property string $body
 * @property string|null $image
 * @property string|null $medium_url
 * @property string|null $dev_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['slug', 'category', 'published_at', 'is_featured', 'title', 'summary', 'body', 'image', 'medium_url', 'dev_url'])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory, HasPublicationDate, HasTranslations;

    /**
     * Published articles the home "Contenido" section needs before it is shown.
     */
    public const HOME_MINIMUM = 3;

    /**
     * Words per minute used to estimate the reading time.
     */
    public const WORDS_PER_MINUTE = 200;

    /**
     * Attributes stored as {"es": ..., "en": ...}.
     *
     * @var list<string>
     */
    public array $translatable = ['title', 'summary', 'body'];

    /**
     * Remove the article's cover image from the public disk when it is deleted.
     */
    protected static function booted(): void
    {
        static::deleted(function (Article $article): void {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ArticleCategory::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            ...array_fill_keys($this->translatable, 'array'),
        ];
    }

    /**
     * Resolve the route key used by /blog/{article}.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Whether at least one article is published, which is what makes the blog visible on the site.
     */
    public static function hasPublished(?ArticleCategory $category = null): bool
    {
        return static::query()
            ->published()
            ->when($category, fn (Builder $query) => $query->where('category', $category))
            ->exists();
    }

    /**
     * Public URL of the cover image, or null when the article has none.
     */
    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    /**
     * Where else the article is published, as network name => URL (only the filled ones).
     *
     * @return array<string, string>
     */
    public function alsoPublishedOn(): array
    {
        return array_filter(['Medium' => $this->medium_url, 'DEV' => $this->dev_url]);
    }

    /**
     * Estimated minutes to read the body in the current locale (at least one).
     */
    public function readingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / self::WORDS_PER_MINUTE));
    }

    /**
     * The body rendered from Markdown. Raw HTML is escaped and unsafe links are dropped.
     */
    public function bodyHtml(): string
    {
        return Str::markdown((string) $this->body, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Newest first by publication date; drafts last.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function latestPublished(Builder $query): void
    {
        $query->orderByRaw('published_at is null')->orderByDesc('published_at')->orderByDesc('id');
    }
}
