<?php

namespace Database\Factories\Users\UserProjects;

use App\Models\User;
use App\Models\Users\Freelancer\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UserSkillsFactory extends Factory
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
            'skill_id' => Skill::factory()
        ];
    }
}
