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

            'name_EN' => 'Engineering',
            'name_AR' => 'هندسة',
            'image'   => 'images/Majors_Pic/engineering.png'
            ]);
        Major::create([
            'name_EN' => 'Medical',
            'name_AR' => 'طبي',
            'image'   => 'images/Majors_Pic/medical.png'
        ]);
        Major::create([
            'name_EN' => 'Technology',
            'name_AR' => 'تكنولوجيا',
            'image'   => 'images/Majors_Pic/technology.png'
        ]);
        Major::create([
           'name_EN' => 'Designing',
           'name_AR' => 'تصميم',
           'image'   => 'images/Majors_Pic/designing2.png'
        ]);
        Major::create([
            'name_EN' => 'Customer services',
            'name_AR' => 'خدمة العملاء',
            'image'   => 'images/Majors_Pic/servicing.png'
           
        ]);
       
    }
}
