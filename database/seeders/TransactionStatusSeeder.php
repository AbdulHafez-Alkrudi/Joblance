<?php

namespace Database\Seeders;

use App\Models\TransactionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = [
            ['name' => 'pending'],
            ['name' => 'complete'],
            ['name' => 'cancle'],
            // you can add more here
        ];
        foreach($status as $temp){
            TransactionStatus::create($temp);
        }
    }
}
