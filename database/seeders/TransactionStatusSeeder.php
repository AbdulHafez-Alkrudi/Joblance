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
            [
                'name_EN' => 'pending',
                'name_AR' => 'قيد الانتظار'
            ],
            [
                'name_EN' => 'complete',
                'name_AR' => 'مكتمل'
            ],
            [
                'name_EN' => 'cancle',
                'name_AR' => 'ملغي'
            ]
            // you can add more here
        ];
        foreach($status as $temp){
            TransactionStatus::create($temp);
        }
    }
}
