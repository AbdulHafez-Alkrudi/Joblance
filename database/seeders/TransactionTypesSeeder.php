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
            ['name' => 'charge by PayPal'],
            ['name' => 'recieve by PayPal'],
            ['name' => 'charge Cash'],
            ['name' => 'recieve Cash'],
            // you can add more here
        ];
        foreach($types as $type){
            TransactionTypes::create($type);
        }
    }
}
