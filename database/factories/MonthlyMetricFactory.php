<?php

namespace Database\Factories;

use App\Models\GrowthPlan;
use App\Models\MonthlyMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonthlyMetric>
 */
class MonthlyMetricFactory extends Factory
{
    /**
     * Define the model's default state: the current month's figures.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'growth_plan_id' => GrowthPlan::factory(),
            'month' => now()->startOfMonth()->toDateString(),
            'publications' => fake()->numberBetween(0, 10),
            'linkedin_followers' => fake()->numberBetween(0, 100),
            'talks' => fake()->numberBetween(0, 1),
            'applications' => fake()->numberBetween(0, 8),
        ];
    }
}
