<?php

namespace Database\Factories\Users\Company;

use App\Models\Users\Company\JobDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class JobDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => User::factory()->forCompany(),
            'job_type_id' => $this->faker->numberBetween(1 , 3),
            'experience_level_id' => $this->faker->numberBetween(1 , 4),
            'remote_id' => $this->faker->numberBetween(1 , 3),
            'major_id' => $this->faker->numberBetween(1 ,4),
            'title' => $this->faker->name,
            'salary' => $this->faker->numberBetween(100 , 10000),
            'location' => $this->faker->city,
            'about_job' => $this->faker->realText,
            'requirements' => $this->faker->realText,
            'additional_information' => $this->faker->realText,
            'active' => $this->faker->boolean,
            'show_number_of_employees' => $this->faker->boolean,
            'show_about_the_company' => $this->faker->boolean,
        ];
    }
}
