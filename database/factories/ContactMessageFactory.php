<?php

namespace Database\Factories;

use App\Enums\ContactTopic;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state: a Spanish message about a talk.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'topic' => ContactTopic::Talk,
            'message' => fake()->paragraph(),
            'locale' => 'es',
            'page' => '/#contacto',
            'read_at' => null,
        ];
    }

    /**
     * A project inquiry, which AraguaneyCode also receives.
     */
    public function project(): static
    {
        return $this->state(fn (array $attributes): array => ['topic' => ContactTopic::Project]);
    }

    /**
     * A message already read in the dashboard.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes): array => ['read_at' => now()]);
    }
}
