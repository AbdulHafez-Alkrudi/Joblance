<?php

namespace Database\Seeders;

use App\Models\Users\Company\Remote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RemoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Remote::create([
            'name_EN' => 'On-site',
            'name_AR' => 'دوام مكتبي'
        ]);

        Remote::create([
            'name_EN' => 'Remote',
            'name_AR' => 'عن بعد'
        ]);

        Remote::create([
            'name_EN' => 'Hybrid',
            'name_AR' => 'دوام جزئي من مقر الشركة'
        ]);
    }
}
