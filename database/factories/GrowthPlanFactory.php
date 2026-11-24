<?php

namespace Database\Factories;

use App\Models\GrowthPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GrowthPlan>
 */
class GrowthPlanFactory extends Factory
{
    /**
     * Define the model's default state: a 90-day plan on its day 38.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Plan '.fake()->unique()->numberBetween(1, 1000),
            'starts_on' => now()->subDays(37)->toDateString(),
            'days' => 90,
            'visits_per_month' => 500,
            'araguaneycode_clicks' => 50,
            'business_conversations' => 8,
            'publications' => 24,
            'linkedin_followers' => 300,
            'talks' => 1,
            'applications' => 15,
        ];
    }

    /**
     * A plan that ended before today.
     */
    public function finished(): static
    {
        return $this->state(fn (array $attributes): array => ['starts_on' => now()->subDays(120)->toDateString()]);
    }

    /**
     * A plan that has not started yet.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes): array => ['starts_on' => now()->addDays(10)->toDateString()]);
    }
}
