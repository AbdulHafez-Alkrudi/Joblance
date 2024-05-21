<?php

namespace Database\Factories;

use App\Models\UserProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UserProjectImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => UserProject::factory(),
            'image_path' => $this->faker->image
        ];
    }
}
