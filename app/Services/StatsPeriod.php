<?php

namespace App\Services;

use App\Models\GrowthPlan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;

/**
 * The date range the dashboard figures and reports cover, and the plan whose goals apply to it.
 *
 * Keys: "this_month", "last_month", "plan-{id}" (a whole plan) or a month as "Y-m".
 */
final class StatsPeriod
{
    public const PRESETS = ['this_month', 'last_month'];

    public function __construct(
        public readonly string $key,
        public readonly CarbonImmutable $start,
        public readonly CarbonImmutable $end,
        public readonly ?GrowthPlan $plan = null,
    ) {}

    /**
     * Build a period from its key; unknown keys (or plans that no longer exist) fall back to this month.
     */
    public static function fromKey(string $key): self
    {
        $today = Date::now();

        if (preg_match('/^plan-(\d+)$/', $key, $matches) && ($plan = GrowthPlan::query()->find($matches[1]))) {
            return self::plan($plan);
        }

        return match (true) {
            $key === 'last_month' => self::month($today->subMonthNoOverflow(), 'last_month'),
            (bool) preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $key) => self::month(CarbonImmutable::createFromFormat('!Y-m', $key)),
            default => self::month($today, 'this_month'),
        };
    }

    /**
     * Whether a key names a period the dashboard can show.
     */
    public static function isValidKey(string $key): bool
    {
        return in_array($key, self::PRESETS, true)
            || (preg_match('/^plan-(\d+)$/', $key, $matches) && GrowthPlan::query()->whereKey($matches[1])->exists());
    }

    /**
     * The calendar month that contains the given date; its plan is the one running at its last day with data.
     */
    public static function month(CarbonImmutable $date, ?string $key = null): self
    {
        $start = $date->startOfMonth();
        $end = $date->endOfMonth();

        return new self($key ?? $date->format('Y-m'), $start, $end, GrowthPlan::forDate($end->min(Date::now())->max($start)));
    }

    /**
     * The whole length of a plan.
     */
    public static function plan(GrowthPlan $plan): self
    {
        return new self('plan-'.$plan->id, $plan->starts_on->startOfDay(), $plan->endsOn(), $plan);
    }

    /**
     * Whether the period is exactly one calendar month.
     */
    public function isCalendarMonth(): bool
    {
        return $this->start->isSameDay($this->start->startOfMonth()) && $this->end->isSameDay($this->start->endOfMonth());
    }

    /**
     * Length of the period in days.
     */
    public function days(): int
    {
        return (int) floor($this->start->diffInDays($this->end)) + 1;
    }

    /**
     * The last day with data: the end of the period or today, whichever comes first.
     */
    public function lastDayWithData(): CarbonImmutable
    {
        return $this->end->min(Date::now()->endOfDay());
    }

    /**
     * The month a monthly report for this period covers ("Y-m"); a plan uses the current month.
     */
    public function reportMonth(): string
    {
        return $this->isCalendarMonth() ? $this->start->format('Y-m') : Date::now()->format('Y-m');
    }

    /**
     * Human name, e.g. "Este mes", "noviembre de 2026" or the plan name with its dates.
     */
    public function label(): string
    {
        return match (true) {
            $this->key === 'this_month' => __('This month'),
            $this->key === 'last_month' => __('Last month'),
            str_starts_with($this->key, 'plan-') && $this->plan !== null => $this->plan->label(),
            default => $this->start->settings(['locale' => app()->getLocale()])->isoFormat(__('MMMM YYYY')),
        };
    }

    /**
     * Range of the period for file names, e.g. "2026-11-01_2026-11-30".
     */
    public function slug(): string
    {
        return $this->start->toDateString().'_'.$this->end->toDateString();
    }
}
