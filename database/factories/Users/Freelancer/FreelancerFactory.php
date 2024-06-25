<?php

namespace Database\Factories\Users\Freelancer;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

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
        /*$image = $this->faker->image();
        $path  = 'freelancer/'.basename($image);
        Storage::disk('public')->put($path , file_get_contents($image));
        @unlink($image);*/
        return [
            'study_case_id' => $this->faker->numberBetween(1 , 5),
            'first_name'    => $this->faker->firstName,
            'last_name'     => $this->faker->lastName,
            'birth_date'    => $this->faker->date,
            'location'      => $this->faker->country,
            'major_id'      => $this->faker->numberBetween(1 , 5),
            'open_to_work'  => $this->faker->boolean,
            'image'         => null,
            'bio'           => $this->faker->realText,
            'gender'        => $this->faker->randomElement(['male' , 'female'])
        ];
    }
}
