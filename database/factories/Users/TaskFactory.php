<?php

namespace Database\Factories\Users;

use App\Models\User;
use App\Models\Users\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
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
            'major_id' => $this->faker->numberBetween(1 , 5),
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
