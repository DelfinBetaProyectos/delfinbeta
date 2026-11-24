<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\GrowthPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

/**
 * A personal brand plan (usually 90 days). Plans follow one another without overlapping;
 * the dashboard measures the active one. Goals are totals for the whole plan, except `visits_per_month`.
 *
 * @property int $id
 * @property string $name
 * @property CarbonImmutable $starts_on
 * @property int $days
 * @property int $visits_per_month
 * @property int $araguaneycode_clicks
 * @property int $business_conversations
 * @property int $publications
 * @property int $linkedin_followers
 * @property int $talks
 * @property int $applications
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'starts_on', 'days', 'visits_per_month', 'araguaneycode_clicks', 'business_conversations', 'publications', 'linkedin_followers', 'talks', 'applications'])]
class GrowthPlan extends Model
{
    /** @use HasFactory<GrowthPlanFactory> */
    use HasFactory;

    /**
     * Every goal of a plan, in the order the plan form shows them.
     *
     * @var list<string>
     */
    public const GOALS = ['visits_per_month', 'araguaneycode_clicks', 'business_conversations', 'publications', 'linkedin_followers', 'talks', 'applications'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'days' => 'integer',
            ...array_fill_keys(self::GOALS, 'integer'),
        ];
    }

    /**
     * Figures entered by hand at each monthly review of the plan.
     *
     * @return HasMany<MonthlyMetric, $this>
     */
    public function monthlyMetrics(): HasMany
    {
        return $this->hasMany(MonthlyMetric::class);
    }

    /**
     * The plan the dashboard follows: the active one, else the last one that ended, else the next one.
     */
    public static function current(): ?self
    {
        $plans = self::query()->orderBy('starts_on')->get();
        $today = Date::now();

        return $plans->first(fn (self $plan): bool => $plan->contains($today))
            ?? $plans->filter(fn (self $plan): bool => $plan->starts_on->lte($today))->last()
            ?? $plans->first();
    }

    /**
     * The plan running on the given day, if any.
     */
    public static function forDate(CarbonInterface $date): ?self
    {
        return self::query()
            ->whereDate('starts_on', '<=', $date)
            ->orderByDesc('starts_on')
            ->get()
            ->first(fn (self $plan): bool => $plan->contains($date));
    }

    /**
     * The last day of the plan, at its end.
     */
    public function endsOn(): CarbonImmutable
    {
        return $this->starts_on->addDays($this->days - 1)->endOfDay();
    }

    /**
     * Whether the plan runs on the given day.
     */
    public function contains(CarbonInterface $date): bool
    {
        return $date->between($this->starts_on->startOfDay(), $this->endsOn());
    }

    /**
     * Whether the dates overlap another plan (`$ignoring` is the plan being edited).
     */
    public static function overlaps(CarbonImmutable $startsOn, int $days, ?self $ignoring = null): bool
    {
        $endsOn = $startsOn->addDays($days - 1)->endOfDay();

        return self::query()
            ->when($ignoring, fn ($query, self $plan) => $query->whereKeyNot($plan->getKey()))
            ->get()
            ->contains(fn (self $plan): bool => $plan->starts_on->lte($endsOn) && $plan->endsOn()->gte($startsOn));
    }

    /**
     * "upcoming", "active" or "finished".
     */
    public function status(): string
    {
        $today = Date::now();

        return match (true) {
            $this->starts_on->gt($today) => 'upcoming',
            $this->endsOn()->lt($today) => 'finished',
            default => 'active',
        };
    }

    /**
     * The plan day of today (1 on the first day), kept between 1 and the plan length.
     */
    public function currentDay(): int
    {
        $day = (int) floor($this->starts_on->diffInDays(Date::now()->startOfDay(), false)) + 1;

        return max(1, min($this->days, $day));
    }

    /**
     * Where the goals should be today if they advance evenly, from 0 to 1.
     */
    public function expectedProgress(): float
    {
        return match ($this->status()) {
            'upcoming' => 0.0,
            'finished' => 1.0,
            default => $this->currentDay() / $this->days,
        };
    }

    /**
     * The target of a goal, e.g. "araguaneycode_clicks".
     */
    public function goal(string $name): int
    {
        return in_array($name, self::GOALS, true) ? (int) $this->getAttribute($name) : 0;
    }

    /**
     * First day of every month the plan touches, up to the current month.
     *
     * @return list<CarbonImmutable>
     */
    public function monthsSoFar(): array
    {
        $months = [];
        $lastMonth = Date::now()->min($this->endsOn())->startOfMonth();

        for ($month = $this->starts_on->startOfMonth(); $month->lte($lastMonth); $month = $month->addMonth()) {
            $months[] = $month;
        }

        return $months;
    }

    /**
     * Name with dates, e.g. "Plan 1 · 17 oct 2026 – 14 ene 2027", in the current locale.
     */
    public function label(): string
    {
        $locale = app()->getLocale();

        return $this->name.' · '.$this->starts_on->settings(['locale' => $locale])->isoFormat('D MMM YYYY')
            .' – '.$this->endsOn()->settings(['locale' => $locale])->isoFormat('D MMM YYYY');
    }
}
