<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 


class PurchasesTableSeeder extends Seeder
{
    public function run() 
    {
        DB::table('purchases')->insert([
            ['vendor_id' => 1, 'user_id' => 1, 'project_id' => 1, 'total_amount' => 3000000, 'purchase_date' => '2023-06-01', 'status' => 'Completed'],
            ['vendor_id' => 2, 'user_id' => 1, 'project_id' => 2, 'total_amount' => 5000000, 'purchase_date' => '2023-06-02', 'status' => 'Pending'], 
        ]);
    }
}
