<?php

namespace Database\Seeders;

use App\Models\Payment\Price;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Price::create([
            'name_EN' => 'Annual Subscription',
            'name_AR' => 'اشتراك سنوي',
            'price'   => 100
        ]);

        Price::create([
            'name_EN' => 'Monthly Subscription',
            'name_AR' => 'اشتراك شهري',
            'price'   => 100
        ]);

        Price::create([
            'name_EN' => 'Important Job',
            'name_AR' => 'منشور هام',
            'price'   => 100
        ]);

        Price::create([
            'name_EN' => 'Fee',
            'name_AR' => 'فائدة',
            'price'   => 100
        ]);
    }
}
