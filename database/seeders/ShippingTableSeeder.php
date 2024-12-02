<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ShippingTableSeeder extends Seeder
{
    public function run()  
    {
        DB::table('shipping')->insert([
            ['purchase_detail_id' => 1, 'project_id' => 1, 'vendor_id' => 1, 'customer_id' => 1, 'shipping_status' => 'Pending', 'Number_receipt' => 12345],
            ['purchase_detail_id' => 2, 'project_id' => 2, 'vendor_id' => 2, 'customer_id' => 2, 'shipping_status' => 'Completed', 'Number_receipt' => 67890],
        ]); 
    }
}
