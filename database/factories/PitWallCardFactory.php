<?php

namespace Database\Factories;

use App\Models\PitWallCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PitWallCard>
 */
class PitWallCardFactory extends Factory
{
    public function definition(): array
    {
        $sector = fake()->numberBetween(1, 4);

        return [
            'sector_label' => "SECTOR {$sector}",
            'title' => fake()->words(2, true),
            'body' => fake()->sentence(12),
            'image' => null,
            'sort' => $sector,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
