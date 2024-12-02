<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('vendors')->insert([
            [
                'user_id' => 1,
                'vendor_name' => 'Vendor A',
                'vendor_email' => 'vendora@example.com',
                'vendor_phone' => '08987654321',
                'vendor_address' => 'Jl. Vendor No.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'vendor_name' => 'Vendor B',
                'vendor_email' => 'vendorb@example.com',
                'vendor_phone' => '08987654322',
                'vendor_address' => 'Jl. Vendor No.2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
