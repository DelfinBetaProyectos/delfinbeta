<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state: a published project with both locales filled.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(3), '.');

        return [
            'slug' => Str::slug($title),
            'sort_order' => 0,
            'published_at' => now()->subDay(),
            'label' => $this->translated('Laravel · AWS'),
            'title' => ['es' => "{$title} (es)", 'en' => "{$title} (en)"],
            'summary' => $this->translated(fake()->sentence()),
            'image' => null,
            'role' => ['es' => 'Arquitecta de soluciones', 'en' => 'Solutions architect'],
            'period' => $this->translated('2024 – 2026'),
            'stack' => $this->translated('Laravel · AWS'),
            'live_url' => null,
            'context' => $this->translated(fake()->paragraph()),
            'problem' => $this->translated(fake()->paragraph()),
            'built' => $this->translated([fake()->sentence(), fake()->sentence()]),
            'built_images' => null,
            'stats' => $this->translated([['value' => '+12', 'label' => 'ligas']]),
            'result' => $this->translated(fake()->paragraph()),
        ];
    }

    /**
     * An unpublished draft.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => null]);
    }

    /**
     * A project scheduled for next week.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => now()->addWeek()]);
    }

    /**
     * A project with a public link to the live platform.
     */
    public function live(): static
    {
        return $this->state(fn (array $attributes): array => ['live_url' => 'https://example.com']);
    }

    /**
     * Use the same value for every supported locale.
     *
     * @return array<string, mixed>
     */
    private function translated(mixed $value): array
    {
        return array_fill_keys(config('site.locales'), $value);
    }
}
