<?php

namespace Database\Factories\Users\Freelancer;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ExperienceLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=> $this->faker->name
        ];
    }
}
