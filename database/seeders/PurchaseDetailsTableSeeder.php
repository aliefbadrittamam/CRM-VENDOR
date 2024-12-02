<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class PurchaseDetailsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('purchase_details')->insert([
            ['purchase_id' => 1, 'product_id' => 1, 'quantity' => 3, 'subtotal' => 3000000],
            ['purchase_id' => 2, 'product_id' => 2, 'quantity' => 2, 'subtotal' => 4000000],
            ['purchase_id' => 2, 'product_id' => 1, 'quantity' => 1, 'subtotal' => 1000000],
        ]);
    }
}