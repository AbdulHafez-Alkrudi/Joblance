<?php

namespace Database\Seeders;

use App\Models\Users\Freelancer\StudyCase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudyCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        StudyCase::create([
            'name_EN' => 'High school',
            'name_AR' => 'ثانوية'
        ]);
        StudyCase::create([
            'name_EN' => 'Under graduated',
            'name_AR' => 'طالب جامعي'
        ]);
        StudyCase::create([
            'name_EN' => 'Graduated',
            'name_AR' => 'خريج جامعي'
        ]);
        StudyCase::create([
            'name_EN' => 'Master',
            'name_AR' => 'ماجستير'
        ]);
        StudyCase::create([
            'name_EN' => 'PHD',
            'name_AR' => 'دكنوراه'
        ]);

    }
}
