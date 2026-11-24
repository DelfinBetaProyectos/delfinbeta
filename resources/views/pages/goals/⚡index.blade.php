<?php

use App\Livewire\Forms\GrowthPlanForm;
use App\Models\GrowthPlan;
use App\Models\MonthlyMetric;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('90-day goals')] class extends Component {
    /**
     * The plan shown; empty for the current one.
     */
    #[Url(as: 'plan', except: null)]
    public ?int $planId = null;

    public GrowthPlanForm $form;

    /**
     * Figures entered by hand for the plan shown, per month ("Y-m") and figure.
     *
     * @var array<string, array<string, int|string>>
     */
    public array $figures = [];

    /**
     * Show the requested plan (or the current one) with its figures.
     */
    public function mount(): void
    {
        $this->planId = $this->plan?->id;
        $this->loadFigures();
    }

    /**
     * Load the figures of the newly picked plan.
     */
    public function updatedPlanId(): void
    {
        unset($this->plan);
        $this->planId = $this->plan?->id;
        $this->loadFigures();
    }

    /**
     * Every plan, newest first.
     *
     * @return Collection<int, GrowthPlan>
     */
    #[Computed]
    public function plans(): Collection
    {
        return GrowthPlan::query()->orderByDesc('starts_on')->get();
    }

    /**
     * The plan shown: the requested one if it exists, else the current one.
     */
    #[Computed]
    public function plan(): ?GrowthPlan
    {
        return ($this->planId ? GrowthPlan::query()->find($this->planId) : null) ?? GrowthPlan::current();
    }

    /**
     * Open the form for a new plan.
     */
    public function createPlan(): void
    {
        $this->form->setNew();

        Flux::modal('plan-form')->show();
    }

    /**
     * Open the form for the plan shown.
     */
    public function editPlan(): void
    {
        $this->form->setPlan($this->plan ?? abort(404));

        Flux::modal('plan-form')->show();
    }

    /**
     * Save the plan in the form and show it.
     */
    public function savePlan(): void
    {
        $isNew = $this->form->plan === null;
        $plan = $this->form->save();

        unset($this->plans, $this->plan);
        $this->planId = $plan->id;
        $this->loadFigures();

        Flux::modal('plan-form')->close();
        Flux::toast(variant: 'success', text: $isNew ? __('Plan created.') : __('Plan saved.'));
    }

    /**
     * Delete the plan shown, with its figures, and go back to the current plan.
     */
    public function deletePlan(): void
    {
        ($this->plan ?? abort(404))->delete();

        unset($this->plans, $this->plan);
        $this->planId = null;
        $this->planId = $this->plan?->id;
        $this->loadFigures();

        Flux::modal('delete-plan')->close();
        Flux::toast(variant: 'success', text: __('Plan deleted.'));
    }

    /**
     * Save the figures of every month shown for the plan.
     */
    public function saveFigures(): void
    {
        $plan = $this->plan ?? abort(404);
        $months = array_map(fn ($month): string => $month->format('Y-m'), $plan->monthsSoFar());

        $validated = $this->validate([
            'figures' => ['array'],
            'figures.*' => ['array'],
            ...collect(MonthlyMetric::FIGURES)->mapWithKeys(fn (string $figure): array => [
                "figures.*.{$figure}" => ['required', 'integer', 'min:0', 'max:100000'],
            ])->all(),
        ], attributes: collect(MonthlyMetric::FIGURES)->mapWithKeys(fn (string $figure): array => [
            "figures.*.{$figure}" => __("goals.{$figure}"),
        ])->all());

        foreach ($validated['figures'] as $month => $values) {
            if (! in_array($month, $months, true)) {
                continue;
            }

            $metric = $plan->monthlyMetrics()->whereDate('month', $month.'-01')->first()
                ?? new MonthlyMetric(['growth_plan_id' => $plan->id, 'month' => $month.'-01']);
            $metric->fill(collect($values)->only(MonthlyMetric::FIGURES)->map(fn ($value): int => (int) $value)->all())->save();
        }

        Flux::toast(variant: 'success', text: __('Figures saved.'));
    }

    /**
     * Total of a figure across the months shown, for the totals row.
     */
    public function total(string $figure): int
    {
        return (int) collect($this->figures)->sum(fn (array $values): int => (int) ($values[$figure] ?? 0));
    }

    /**
     * Fill the figures of the plan shown (zeros for months not entered yet).
     */
    private function loadFigures(): void
    {
        $this->resetErrorBag();
        $this->figures = [];

        if ($this->plan === null) {
            return;
        }

        $saved = $this->plan->monthlyMetrics()->get()->keyBy(fn (MonthlyMetric $metric): string => $metric->month->format('Y-m'));

        foreach ($this->plan->monthsSoFar() as $month) {
            $key = $month->format('Y-m');

            foreach (MonthlyMetric::FIGURES as $figure) {
                $this->figures[$key][$figure] = $saved->get($key)?->{$figure} ?? 0;
            }
        }
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1">{{ __('90-day goals') }}</flux:heading>
            <flux:subheading size="lg">{{ __('Each plan has its goals; plans follow one another. Enter the figures the site cannot measure at the review on the first Sunday.') }}</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="createPlan">{{ __('New plan') }}</flux:button>
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    @if ($this->plan === null)
        <flux:text>{{ __('There are no plans yet. Create the first one to follow its goals.') }}</flux:text>
    @else
        @php($plan = $this->plan)

        <div class="mb-6 flex flex-wrap items-end gap-4">
            <div class="w-80">
                <flux:select wire:model.live="planId" :label="__('Plan')">
                    @foreach ($this->plans as $planOption)
                        <flux:select.option :value="$planOption->id">{{ $planOption->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex items-center gap-2 pb-1">
                @switch($plan->status())
                    @case('upcoming') <flux:badge color="zinc" size="sm">{{ __('Upcoming') }}</flux:badge> @break
                    @case('finished') <flux:badge color="zinc" size="sm">{{ __('Finished') }}</flux:badge> @break
                    @default <flux:badge color="sky" size="sm">{{ __('Day :day of :days', ['day' => $plan->currentDay(), 'days' => $plan->days]) }}</flux:badge>
                @endswitch
            </div>

            <flux:spacer />

            <flux:button variant="ghost" icon="pencil-square" wire:click="editPlan">{{ __('Edit plan') }}</flux:button>
            <flux:modal.trigger name="delete-plan">
                <flux:button variant="ghost" icon="trash" class="text-red-600! dark:text-red-400!">{{ __('Delete') }}</flux:button>
            </flux:modal.trigger>
        </div>

        <dl class="mb-8 grid gap-4 sm:grid-cols-3 lg:grid-cols-7">
            @foreach (GrowthPlan::GOALS as $goal)
                <div wire:key="plan-goal-{{ $goal }}" class="rounded-lg border border-zinc-200 p-3 dark:border-white/10">
                    <dt class="text-xs text-zinc-500 dark:text-zinc-400">{{ __("goals.goals.{$goal}") }}</dt>
                    <dd class="mt-1 text-lg font-semibold text-zinc-800 dark:text-white">{{ $plan->goal($goal) }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($plan->status() === 'upcoming')
            <flux:text>{{ __('The plan has not started: its figures can be entered from :date.', ['date' => $plan->starts_on->settings(['locale' => app()->getLocale()])->isoFormat('D MMM YYYY')]) }}</flux:text>
        @else
            <form wire:submit="saveFigures" class="space-y-6">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Month') }}</flux:table.column>
                        @foreach (MonthlyMetric::FIGURES as $figure)
                            <flux:table.column>{{ __("goals.{$figure}") }}</flux:table.column>
                        @endforeach
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($plan->monthsSoFar() as $month)
                            @php($monthKey = $month->format('Y-m'))
                            @php($monthName = $month->settings(['locale' => app()->getLocale()])->isoFormat('MMMM YYYY'))
                            <flux:table.row :key="'month-'.$plan->id.'-'.$monthKey">
                                <flux:table.cell class="font-medium whitespace-nowrap text-zinc-800 capitalize dark:text-white">{{ $monthName }}</flux:table.cell>
                                @foreach (MonthlyMetric::FIGURES as $figure)
                                    <flux:table.cell>
                                        <div class="w-28">
                                            <flux:input type="number" min="0" max="100000" wire:model="figures.{{ $monthKey }}.{{ $figure }}" :aria-label="__('goals.'.$figure).' · '.$monthName" />
                                        </div>
                                        <flux:error name="figures.{{ $monthKey }}.{{ $figure }}" />
                                    </flux:table.cell>
                                @endforeach
                            </flux:table.row>
                        @endforeach

                        <flux:table.row>
                            <flux:table.cell class="font-semibold text-zinc-800 dark:text-white">{{ __('Total / goal') }}</flux:table.cell>
                            @foreach (MonthlyMetric::FIGURES as $figure)
                                <flux:table.cell class="font-semibold text-zinc-800 dark:text-white">
                                    {{ $this->total($figure) }} / {{ $plan->goal($figure) }}
                                    <div class="text-xs font-normal text-zinc-500">{{ __("goals.hints.{$figure}") }}</div>
                                </flux:table.cell>
                            @endforeach
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">{{ __('Save figures') }}</flux:button>
                </div>
            </form>
        @endif

        <flux:modal name="delete-plan" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Delete :plan?', ['plan' => $plan->name]) }}</flux:heading>
                    <flux:text class="mt-2">{{ __('The plan and the figures entered for it will be permanently deleted. This cannot be undone.') }}</flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button variant="danger" wire:click="deletePlan">{{ __('Delete') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <flux:modal name="plan-form" class="w-full max-w-2xl">
        <form wire:submit="savePlan" class="space-y-6">
            <flux:heading size="lg">{{ $form->plan ? __('Edit plan') : __('New plan') }}</flux:heading>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:input wire:model="form.name" :label="__('Name')" required />
                <flux:input wire:model="form.starts_on" type="date" :label="__('Start date')" required />
                <flux:input wire:model="form.days" type="number" min="7" max="365" :label="__('Length in days')" required />
            </div>

            <div>
                <flux:heading size="sm">{{ __('Goals') }}</flux:heading>
                <flux:text size="sm" class="mt-1">{{ __('Totals for the whole plan, except visits, which are per month.') }}</flux:text>
                <div class="mt-3 grid gap-4 sm:grid-cols-2">
                    @foreach (GrowthPlan::GOALS as $goal)
                        <flux:input wire:key="form-goal-{{ $goal }}" wire:model="form.goals.{{ $goal }}" type="number" min="0" :label="__('goals.goals.'.$goal)" required />
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save plan') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</section>
