<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectImage>
 */
class ProjectImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'path' => 'projects/'.fake()->uuid().'.webp',
            'caption' => fake()->optional()->sentence(4),
            'sort' => fake()->numberBetween(0, 10),
        ];
    }
}
