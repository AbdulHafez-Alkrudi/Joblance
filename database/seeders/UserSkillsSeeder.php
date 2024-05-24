<?php

namespace Database\Seeders;

use App\Models\UserSkills;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserSkills::factory(5)->create();
    }
}
