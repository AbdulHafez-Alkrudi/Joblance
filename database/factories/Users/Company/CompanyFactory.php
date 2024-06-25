<?php

namespace Database\Factories\Users\Company;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

/**
 * @extends Factory
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
        $image = $this->faker->image();
        $path  = 'company/'.basename($image);
        Storage::disk('public')->put($path , file_get_contents($image));
        @unlink($image);
        return [
            'name'              => $this->faker->company,
            'location'          => $this->faker->country,
            'major_id'          => $this->faker->numberBetween(1 , 5),
            'num_of_employees'  => $this->faker->numberBetween(10 , 1000),
            'description'       => $this->faker->text,
            'image'             => $path
        ];
    }
}
