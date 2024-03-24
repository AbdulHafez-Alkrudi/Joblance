<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'phone_number' => '0980453436',
            'email' => 'auiprt66@gmail.com',
            'password' => bcrypt('SuperSecretPassword'),
            'email_verified' => 1,
            'role_id' => Role::ROLE_ADMINISTRATOR,
        ]);
    }
}
