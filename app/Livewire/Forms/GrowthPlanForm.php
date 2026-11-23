<?php

namespace App\Livewire\Forms;

use App\Models\GrowthPlan;
use Carbon\CarbonImmutable;
use Closure;
use Livewire\Form;

/**
 * Dashboard form for a personal brand plan: its name, dates and goals.
 */
class GrowthPlanForm extends Form
{
    public ?GrowthPlan $plan = null;

    public string $name = '';

    /**
     * First day as "Y-m-d".
     */
    public string $starts_on = '';

    public int|string $days = 90;

    /**
     * Goal values keyed by goal name (see GrowthPlan::GOALS).
     *
     * @var array<string, int|string>
     */
    public array $goals = [];

    /**
     * Start a new plan right after the last one, with its goals as a starting point.
     */
    public function setNew(): void
    {
        $this->reset();
        $this->resetErrorBag();

        $lastPlan = GrowthPlan::query()->orderByDesc('starts_on')->first();

        $this->name = 'Plan '.(GrowthPlan::query()->count() + 1);
        $this->starts_on = ($lastPlan ? $lastPlan->endsOn()->addDay() : now())->toDateString();
        $this->days = $lastPlan->days ?? 90;
        $this->goals = collect(GrowthPlan::GOALS)->mapWithKeys(fn (string $goal): array => [$goal => $lastPlan?->goal($goal) ?? 0])->all();
    }

    /**
     * Fill the form with an existing plan.
     */
    public function setPlan(GrowthPlan $plan): void
    {
        $this->resetErrorBag();

        $this->plan = $plan;
        $this->name = $plan->name;
        $this->starts_on = $plan->starts_on->toDateString();
        $this->days = $plan->days;
        $this->goals = collect(GrowthPlan::GOALS)->mapWithKeys(fn (string $goal): array => [$goal => $plan->goal($goal)])->all();
    }

    /**
     * Validate and save the plan.
     */
    public function save(): GrowthPlan
    {
        $validated = $this->validate();

        $plan = $this->plan ?? new GrowthPlan;
        $plan->fill([
            'name' => $validated['name'],
            'starts_on' => $validated['starts_on'],
            'days' => (int) $validated['days'],
            ...array_map(fn ($value): int => (int) $value, $validated['goals']),
        ])->save();

        return $this->plan = $plan;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'starts_on' => ['required', 'date_format:Y-m-d', $this->noOverlap(...)],
            'days' => ['required', 'integer', 'min:7', 'max:365'],
            'goals' => ['array:'.implode(',', GrowthPlan::GOALS)],
            ...collect(GrowthPlan::GOALS)->mapWithKeys(fn (string $goal): array => [
                "goals.{$goal}" => ['required', 'integer', 'min:0', 'max:1000000'],
            ])->all(),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'name' => __('Name'),
            'starts_on' => __('Start date'),
            'days' => __('Length in days'),
            ...collect(GrowthPlan::GOALS)->mapWithKeys(fn (string $goal): array => ["goals.{$goal}" => __("goals.goals.{$goal}")])->all(),
        ];
    }

    /**
     * Plans follow one another: the dates cannot overlap another plan.
     */
    private function noOverlap(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($this->days) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value)) {
            return;
        }

        if (GrowthPlan::overlaps(CarbonImmutable::parse($value), (int) $this->days, $this->plan)) {
            $fail(__('These dates overlap another plan.'));
        }
    }
}
