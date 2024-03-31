<?php

namespace Database\Seeders;

use App\Models\StudyCase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudyCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudyCase::create(['name' => 'phd']);
    }
}
