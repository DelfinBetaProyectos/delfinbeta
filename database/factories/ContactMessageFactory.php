<?php

namespace Database\Factories;

use App\Enums\ContactTopic;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

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
            'phone' => null,
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
     * A message that includes the sender's phone (digits only, country code first).
     */
    public function withPhone(string $phone = '584243172126'): static
    {
        return $this->state(fn (array $attributes): array => ['phone' => $phone]);
    }

    /**
     * A message already read in the dashboard.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes): array => ['read_at' => now()]);
    }

    /**
     * A message the owner already replied to, the given hours after it arrived.
     */
    public function responded(int $afterHours = 6): static
    {
        return $this->state(fn (array $attributes): array => ['read_at' => now()])
            ->afterMaking(function (ContactMessage $contactMessage) use ($afterHours): void {
                $contactMessage->fill(['responded_at' => Date::parse($contactMessage->created_at ?? now())->addHours($afterHours)]);
            });
    }
}
