<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class SalesDetailsTableSeeder extends Seeder 
{
    public function run()
    {
        DB::table('sales_details')->insert([
            ['sale_id' => 1, 'product_id' => 1, 'quantity' => 2, 'subtotal' => 2000000], 
            ['sale_id' => 1, 'product_id' => 2, 'quantity' => 1, 'subtotal' => 2000000],
            ['sale_id' => 2, 'product_id' => 1, 'quantity' => 3, 'subtotal' => 3000000],
            ['sale_id' => 2, 'product_id' => 2, 'quantity' => 2, 'subtotal' => 4000000],  
        ]);
    }
}
