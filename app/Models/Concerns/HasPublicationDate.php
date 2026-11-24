<?php

namespace App\Models\Concerns;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Publication by date, stored in a nullable `published_at` column:
 * no date is a draft, a past date is published and a future date is scheduled.
 *
 * Days are picked and stored in the app timezone (Maracay). The model must cast
 * `published_at` to `datetime`.
 */
trait HasPublicationDate
{
    /**
     * Whether the model is visible on the site (its publication date has arrived).
     */
    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Whether the model is scheduled for a future date.
     */
    public function isScheduled(): bool
    {
        return $this->published_at !== null && $this->published_at->isFuture();
    }

    /**
     * Publish today: the start of the current day in Maracay, so it shows right away.
     */
    public function publishToday(): void
    {
        $this->update(['published_at' => static::startOfDayInSiteTimezone()]);
    }

    /**
     * Send the model back to draft.
     */
    public function unpublish(): void
    {
        $this->update(['published_at' => null]);
    }

    /**
     * The start of the given day ("Y-m-d", default today) in the app timezone (Maracay).
     */
    public static function startOfDayInSiteTimezone(?string $day = null): CarbonInterface
    {
        return ($day === null ? now() : Carbon::createFromFormat('Y-m-d', $day))->startOfDay();
    }

    /**
     * Only what is visible on the public site.
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
