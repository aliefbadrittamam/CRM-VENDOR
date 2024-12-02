<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('customers')->insert([
            [
                'user_id' => 2, 
                'customer_name' => 'John Doe',
                'customer_email' => 'johndoe@example.com',
                'customer_phone' => '08123456789',
                'customer_address' => 'Jl. Example No.1, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
