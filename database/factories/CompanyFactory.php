<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->company,
            'location'          => $this->faker->country,
            'major_id'          => $this->faker->numberBetween(1 , 5),
            'num_of_employees'  => $this->faker->numberBetween(10 , 1000),
            'description'       => $this->faker->text,
            'image'             => $this->faker->image
        ];
    }
}
