<?php

namespace Database\Factories\Users\UserProjects;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UserProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->forFreelancer(),
            'project_name' => $this->faker->company,
            'project_description' => $this->faker->realText(),
            'link' => $this->faker->url
        ];
    }
}
