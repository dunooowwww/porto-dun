<?php

namespace Database\Factories;

use App\Models\MascotMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MascotMessage>
 */
class MascotMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'section' => fake()->unique()->randomElement(array_keys(MascotMessage::SECTIONS)),
            'message' => fake()->sentence(5),
        ];
    }
}
