<?php

namespace Database\Seeders;

use App\Models\Users\UserProjects\UserProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserProject::factory(5)->create();
    }
}
