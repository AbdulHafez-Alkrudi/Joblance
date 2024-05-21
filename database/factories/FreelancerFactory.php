<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class FreelancerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'study_case_id' => $this->faker->numberBetween(1 , 5),
            'first_name'    => $this->faker->firstName,
            'last_name'     => $this->faker->lastName,
            'birth_date'    => $this->faker->date,
            'location'      => $this->faker->country,
            'major_id'      => $this->faker->numberBetween(1 , 5),
            'open_to_work'  => $this->faker->boolean,
            'image'         => $this->faker->image,
            'bio'           => $this->faker->realText,
            'gender'        => $this->faker->randomElement(['male' , 'female'])
        ];
    }
}
