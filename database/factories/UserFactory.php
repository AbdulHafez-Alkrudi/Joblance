<?php

namespace Database\Factories;

use App\Models\Users\Company\Company;
use App\Models\Users\Freelancer\Freelancer;
use App\Models\User;
use Composer\Autoload\ClassLoader;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'phone_number'=> $this->faker->unique()->phoneNumber,
            'email'       => $this->faker->unique()->name. '@gmail.com',
            'email_verified' => 1,
            'password' => $this->faker->password(8),
            'role_id' => 2
            /*'userable_type' => $this->faker->randomElement(['App\Models\Freelancer' , 'App\Models\Company']),
            'userable_id'   => function(array $user){
                */

        ];
    }
    public function forFreelancer(): Factory
    {
        return $this->state( function(array $attributes) {
           return [
               'userable_id' => Freelancer::factory(),
               'userable_type' => Freelancer::class
           ];
        });
    }
    public function forCompany(): Factory
    {
        return $this->state( function(array $attributes){
                return [
                  'userable_id' => Company::factory(),
                  'userable_type' => Company::class
                ];
            }
        );
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
