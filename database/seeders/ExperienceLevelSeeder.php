<?php

namespace Database\Seeders;

use App\Http\Controllers\ExperienceLevelController;
use App\Models\ExperienceLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExperienceLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExperienceLevel::create([
            'name_EN' => 'Senior',
            'name_AR' => 'خبير'
        ]);
        ExperienceLevel::create([
           'name_EN' => 'Junior',
           'name_AR' => 'مبتدئ'
        ]);
        ExperienceLevel::create([
            'name_EN' => 'Director',
            'name_AR' => 'مدير'
        ]);
        ExperienceLevel::create([
            'name_EN' => 'Internship',
            'name_AR' => 'تدريب'
        ]);

    }
}
