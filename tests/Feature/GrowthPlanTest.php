<?php

use App\Models\GrowthPlan;
use App\Services\BrandStatistics;
use App\Services\StatsPeriod;

beforeEach(function () {
    $this->travelTo(now()->setDate(2026, 11, 23)->setTime(10, 0));
});

it('follows the active plan, else the last finished one, else the next one', function () {
    expect(GrowthPlan::current())->toBeNull();

    $upcoming = GrowthPlan::factory()->create(['starts_on' => '2027-02-01']);
    expect(GrowthPlan::current()->is($upcoming))->toBeTrue();

    $finished = GrowthPlan::factory()->create(['starts_on' => '2026-06-01']);
    expect(GrowthPlan::current()->is($finished))->toBeTrue();

    $active = GrowthPlan::factory()->create(['starts_on' => '2026-10-17']);
    expect(GrowthPlan::current()->is($active))->toBeTrue()
        ->and($active->status())->toBe('active')
        ->and($finished->status())->toBe('finished')
        ->and($upcoming->status())->toBe('upcoming');
});

it('knows its day, end and expected progress', function () {
    $plan = GrowthPlan::factory()->create(['starts_on' => '2026-10-17', 'days' => 90]);

    expect($plan->endsOn()->toDateString())->toBe('2027-01-14')
        ->and($plan->currentDay())->toBe(38)
        ->and(round($plan->expectedProgress(), 2))->toBe(0.42)
        ->and(array_map(fn ($month) => $month->format('Y-m'), $plan->monthsSoFar()))->toBe(['2026-10', '2026-11']);
});

it('detects overlapping dates', function () {
    $plan = GrowthPlan::factory()->create(['starts_on' => '2026-10-17', 'days' => 90]);

    expect(GrowthPlan::overlaps(now()->setDate(2027, 1, 14), 30))->toBeTrue()
        ->and(GrowthPlan::overlaps(now()->setDate(2027, 1, 15), 30))->toBeFalse()
        ->and(GrowthPlan::overlaps(now()->setDate(2026, 9, 1), 46))->toBeFalse()
        ->and(GrowthPlan::overlaps(now()->setDate(2026, 9, 1), 47))->toBeTrue()
        ->and(GrowthPlan::overlaps($plan->starts_on, 90, $plan))->toBeFalse();
});

it('uses the plan running in each month for its goals', function () {
    $first = GrowthPlan::factory()->create(['starts_on' => '2026-07-01', 'visits_per_month' => 300]);
    $second = GrowthPlan::factory()->create(['starts_on' => '2026-10-17', 'visits_per_month' => 500]);

    expect((new BrandStatistics(StatsPeriod::fromKey('2026-08')))->plan->is($first))->toBeTrue()
        ->and((new BrandStatistics(StatsPeriod::fromKey('this_month')))->visitsGoal())->toBe(500)
        ->and((new BrandStatistics(StatsPeriod::fromKey('plan-'.$first->id)))->plan->is($first))->toBeTrue()
        ->and(StatsPeriod::isValidKey('plan-'.$second->id))->toBeTrue()
        ->and(StatsPeriod::isValidKey('plan-999'))->toBeFalse();
});
