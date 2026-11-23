<?php

namespace Database\Factories;

use App\Enums\ArticleCategory;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state: a technical article published yesterday, with both locales filled.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(4), '.');

        return [
            'slug' => Str::slug($title),
            'category' => ArticleCategory::Tech,
            'published_at' => now()->subDay(),
            'is_featured' => false,
            'medium_url' => null,
            'dev_url' => null,
            'image' => null,
            'title' => ['es' => "{$title} (es)", 'en' => "{$title} (en)"],
            'summary' => ['es' => fake()->sentence(), 'en' => fake()->sentence()],
            'body' => ['es' => fake()->paragraphs(3, true), 'en' => fake()->paragraphs(3, true)],
        ];
    }

    /**
     * An unpublished draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => null]);
    }

    /**
     * An article scheduled for next week.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => now()->addWeek()]);
    }

    /**
     * The article highlighted at the top of the blog.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes): array => ['is_featured' => true]);
    }

    /**
     * An article in the given category.
     */
    public function category(ArticleCategory $category): static
    {
        return $this->state(fn (array $attributes): array => ['category' => $category]);
    }
}
