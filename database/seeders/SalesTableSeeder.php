<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 
class SalesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('sales')->insert([  
            ['customer_id' => 1, 'fixed_amount' => 5000000, 'sale_date' => '2023-06-01', 'status' => 'Completed'],
            ['customer_id' => 2, 'fixed_amount' => 7500000, 'sale_date' => '2023-06-02', 'status' => 'Processing'],
        ]);
    }    
}
