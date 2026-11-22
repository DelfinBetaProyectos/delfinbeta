<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * A portfolio project: a card on the home page and a case study at /proyectos/{slug}.
 * Attributes in `$translatable` are stored per locale and read in the current one.
 *
 * @property int $id
 * @property string $slug
 * @property int $sort_order
 * @property bool $is_published
 * @property string $title
 * @property string $summary
 * @property string|null $label
 * @property string|null $image
 * @property string|null $role
 * @property string|null $period
 * @property string|null $stack
 * @property string|null $live_url
 * @property string $context
 * @property string $problem
 * @property list<string>|null $built
 * @property list<string>|null $built_images
 * @property list<array{value: string, label: string}>|null $stats
 * @property string $result
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'slug', 'sort_order', 'is_published', 'title', 'summary', 'label', 'image',
    'role', 'period', 'stack', 'live_url', 'context', 'problem', 'built',
    'built_images', 'stats', 'result',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasTranslations;

    /**
     * Attributes stored as {"es": ..., "en": ...}.
     *
     * @var list<string>
     */
    public array $translatable = [
        'title', 'summary', 'label', 'role', 'period', 'stack', 'context',
        'problem', 'built', 'stats', 'result',
    ];

    /**
     * Remove the project's screenshots from the public disk when it is deleted.
     */
    protected static function booted(): void
    {
        static::deleted(function (Project $project): void {
            Storage::disk('public')->delete(array_filter([$project->image, ...($project->built_images ?? [])]));
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
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'built_images' => 'array',
            ...array_fill_keys($this->translatable, 'array'),
        ];
    }

    /**
     * Resolve the route key used by /proyectos/{project}.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Order projects as they appear on the site.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Only projects visible on the public site.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }
}
