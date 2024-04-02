<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Major::create([
            'name_EN' => 'engineering',
            'name_AR' => 'هندسة'
        ]);
        Major::create([
            'name_EN' => 'medical',
            'name_AR' => 'طبي'
        ]);
        Major::create([
            'name_EN' => 'Technology',
            'name_AR' => 'تقني'
        ]);
        Major::create([
           'name_EN' => 'Designing',
           'name_AR' => 'تصميم'
        ]);
        Major::create([
            'name_EN' => 'Customer services',
            'name_AR' => 'خدمة العملاء'
        ]);
    }
}
