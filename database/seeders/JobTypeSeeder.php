<?php

namespace Database\Seeders;

use App\Models\Users\Company\JobType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobType::create([
            'name_EN' => 'Full time',
            'name_AR' => 'دوام كامل'
        ]);

        JobType::create([
            'name_EN' => 'Part time',
            'name_AR' => 'دوام جزئي'
        ]);

        JobType::create([
            'name_EN' => 'Temporary',
            'name_AR' => 'عقد مؤقت'
        ]);
    }
}
