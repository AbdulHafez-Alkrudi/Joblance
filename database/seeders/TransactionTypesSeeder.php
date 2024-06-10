<?php

namespace Database\Seeders;

use App\Models\TransactionTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name_EN' => 'charge via PayPal',
                'name_AR' => 'شحن عبر PayPal'
            ],
            [
                'name_EN' => 'recieve via PayPal',
                'name_AR' => 'تلقي عبر PayPal'
            ],
            [
                'name_EN' => 'pay Cash',
                'name_AR' => 'دفع نقداً'
            ],
            [
                'name_EN' => 'recieve Cash',
                'name_AR' => 'تلقي نقداً'
            ]
            // you can add more here
        ];
        foreach($types as $type){
            TransactionTypes::create($type);
        }
    }
}
