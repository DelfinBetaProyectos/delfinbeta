<?php

use App\Models\GrowthPlan;
use App\Models\MonthlyMetric;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->travelTo(now()->setDate(2026, 11, 23)->setTime(10, 0));
    app()->setLocale('es');
    $this->plan = GrowthPlan::factory()->create(['name' => 'Plan 1', 'starts_on' => '2026-10-17']);
    $this->actingAs(User::factory()->create(['locale' => 'es']));
});

it('requires authentication', function () {
    auth()->logout();

    $this->get(route('dashboard.goals.index'))->assertRedirect(route('login'));
});

it('shows the current plan with every month so far and its saved figures', function () {
    MonthlyMetric::factory()->for($this->plan)->create(['month' => '2026-10-01', 'publications' => 4, 'linkedin_followers' => 50, 'talks' => 0, 'applications' => 2]);

    Livewire::test('pages::goals.index')
        ->assertSet('planId', $this->plan->id)
        ->assertSet('figures', [
            '2026-10' => ['publications' => 4, 'linkedin_followers' => 50, 'talks' => 0, 'applications' => 2],
            '2026-11' => ['publications' => 0, 'linkedin_followers' => 0, 'talks' => 0, 'applications' => 0],
        ])
        ->assertSeeText('Día 38 de 90');
});

it('saves the figures of each month for the plan shown', function () {
    MonthlyMetric::factory()->for($this->plan)->create(['month' => '2026-10-01', 'publications' => 1]);

    Livewire::test('pages::goals.index')
        ->set('figures.2026-10.publications', '4')
        ->set('figures.2026-11.linkedin_followers', '62')
        ->call('saveFigures')
        ->assertHasNoErrors();

    expect($this->plan->monthlyMetrics()->count())->toBe(2)
        ->and($this->plan->monthlyMetrics()->whereDate('month', '2026-10-01')->value('publications'))->toBe(4)
        ->and($this->plan->monthlyMetrics()->whereDate('month', '2026-11-01')->value('linkedin_followers'))->toBe(62);
});

it('keeps separate figures for a month shared by two plans', function () {
    $previousPlan = GrowthPlan::factory()->create(['name' => 'Plan 0', 'starts_on' => '2026-07-19']);
    MonthlyMetric::factory()->for($previousPlan)->create(['month' => '2026-10-01', 'publications' => 7]);

    Livewire::test('pages::goals.index')
        ->assertSet('figures.2026-10.publications', 0)
        ->set('figures.2026-10.publications', '3')
        ->call('saveFigures')
        ->set('planId', $previousPlan->id)
        ->assertSet('figures.2026-10.publications', 7);

    expect($this->plan->monthlyMetrics()->value('publications'))->toBe(3)
        ->and($previousPlan->monthlyMetrics()->value('publications'))->toBe(7);
});

it('rejects figures that are not whole numbers from zero up', function (mixed $value) {
    Livewire::test('pages::goals.index')
        ->set('figures.2026-11.talks', $value)
        ->call('saveFigures')
        ->assertHasErrors(['figures.2026-11.talks']);

    expect(MonthlyMetric::query()->count())->toBe(0);
})->with(['negative' => -1, 'decimal' => '1.5', 'empty' => '', 'text' => 'dos']);

it('ignores months outside the plan', function () {
    Livewire::test('pages::goals.index')
        ->set('figures.2027-05', ['publications' => 3, 'linkedin_followers' => 0, 'talks' => 0, 'applications' => 0])
        ->call('saveFigures')
        ->assertHasNoErrors();

    expect(MonthlyMetric::query()->whereDate('month', '2027-05-01')->exists())->toBeFalse();
});

it('suggests the next plan right after the last one, with its goals', function () {
    Livewire::test('pages::goals.index')
        ->call('createPlan')
        ->assertSet('form.name', 'Plan 2')
        ->assertSet('form.starts_on', '2027-01-15')
        ->assertSet('form.days', 90)
        ->assertSet('form.goals.publications', 24);
});

it('creates a plan and shows it', function () {
    $component = Livewire::test('pages::goals.index')
        ->call('createPlan')
        ->set('form.goals.publications', 30)
        ->call('savePlan')
        ->assertHasNoErrors();

    $newPlan = GrowthPlan::query()->where('name', 'Plan 2')->sole();

    expect($newPlan->starts_on->toDateString())->toBe('2027-01-15')
        ->and($newPlan->days)->toBe(90)
        ->and($newPlan->publications)->toBe(30);

    $component->assertSet('planId', $newPlan->id)->assertSeeText('El plan todavía no empieza');
});

it('edits the plan shown', function () {
    Livewire::test('pages::goals.index')
        ->call('editPlan')
        ->assertSet('form.starts_on', '2026-10-17')
        ->set('form.name', 'Primer plan')
        ->set('form.days', 100)
        ->call('savePlan')
        ->assertHasNoErrors();

    expect($this->plan->refresh())
        ->name->toBe('Primer plan')
        ->days->toBe(100);
});

it('does not allow plans that overlap', function () {
    Livewire::test('pages::goals.index')
        ->call('createPlan')
        ->set('form.starts_on', '2027-01-14')
        ->call('savePlan')
        ->assertHasErrors(['form.starts_on']);

    expect(GrowthPlan::query()->count())->toBe(1);
});

it('validates the plan fields', function (string $field, mixed $value) {
    Livewire::test('pages::goals.index')
        ->call('createPlan')
        ->set($field, $value)
        ->call('savePlan')
        ->assertHasErrors([$field]);
})->with([
    'empty name' => ['form.name', ''],
    'too short' => ['form.days', 6],
    'too long' => ['form.days', 366],
    'bad date' => ['form.starts_on', '15/01/2027'],
    'negative goal' => ['form.goals.talks', -1],
]);

it('deletes the plan shown with its figures', function () {
    MonthlyMetric::factory()->for($this->plan)->create(['month' => '2026-10-01']);

    Livewire::test('pages::goals.index')
        ->call('deletePlan')
        ->assertSet('planId', null)
        ->assertSeeText('Todavía no hay planes.');

    expect(GrowthPlan::query()->count())->toBe(0)
        ->and(MonthlyMetric::query()->count())->toBe(0);
});
