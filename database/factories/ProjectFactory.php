<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'excerpt' => fake()->sentence(),
            'problem' => fake()->paragraph(),
            'role' => fake()->jobTitle(),
            'process' => '<p>'.fake()->paragraph().'</p>',
            'result' => fake()->sentence(),
            'tech_stack' => fake()->randomElements(['Laravel', 'PHP', 'Python', 'MySQL', 'Tailwind CSS', 'Alpine.js'], 3),
            'year' => fake()->numberBetween(2022, 2026),
            'github_url' => fake()->optional()->url(),
            'demo_url' => fake()->optional()->url(),
            'thumbnail' => null,
            'is_featured' => false,
            'status' => ProjectStatus::Published,
            'sort' => fake()->numberBetween(0, 10),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Draft, 'published_at' => null]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
