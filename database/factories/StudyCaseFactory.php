<?php

namespace Database\Factories;

use App\Models\StudyCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class StudyCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name
        ];
    }
}
