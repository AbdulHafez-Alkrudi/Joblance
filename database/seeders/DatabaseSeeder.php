<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(StudyCaseSeeder::class);
        $this->call(MajorSeeder::class);
        $this->call(ExperienceLevelSeeder::class);
        $this->call(BudgetSeeder::class);
        $this->call(SkillSeeder::class);
        $this->call(UserProjectImageSeeder::class);
        $this->call(UserProjectSeeder::class);
        $this->call(UserSkillsSeeder::class);
        $this->call(TransactionTypesSeeder::class);
        $this->call(TransactionStatusSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(JobTypeSeeder::class);
        $this->call(RemoteSeeder::class);
        $this->call(PriceSeeder::class);
        $this->call(TaskStateSeeder::class);
    }
}
