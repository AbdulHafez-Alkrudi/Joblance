<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'task_title'   => $this->faker->name,
            'about_task'   => $this->faker->text(50),
            'requirements' => $this->faker->text(50),
            'additional_information' => $this->faker->text(50),
            'task_duration' => $this->faker->numberBetween(10 , 100),
            'budget_min'    => $this->faker->numberBetween(1 , 1000),
            'budget_max'    => $this->faker->numberBetween(1000 , 2000)
        ];
    }
}
