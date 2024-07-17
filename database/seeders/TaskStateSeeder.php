<?php

namespace Database\Seeders;

use App\Models\Users\TaskState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskState::create([
            'name_EN' => 'Pending',
            'name_AR' => 'قيد المعالجة'
        ]);

        TaskState::create([
            'name_EN' => 'Done',
            'name_AR' => 'تمت'
        ]);

        TaskState::create([
            'name_EN' => 'Failed',
            'name_AR' => 'فشل'
        ]);
    }
}
