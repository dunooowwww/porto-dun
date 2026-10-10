<?php

namespace Database\Factories;

use App\Enums\ExperienceType;
use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-4 years', '-1 year');

        return [
            'organization' => fake()->company(),
            'role' => fake()->jobTitle(),
            'type' => fake()->randomElement(ExperienceType::cases()),
            'started_at' => $startedAt,
            'ended_at' => fake()->dateTimeBetween($startedAt, 'now'),
            'achievement' => fake()->optional()->sentence(),
            'logo' => null,
            'sort' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }

    /** Masih berjalan sampai sekarang. */
    public function current(): static
    {
        return $this->state(fn () => ['ended_at' => null]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
