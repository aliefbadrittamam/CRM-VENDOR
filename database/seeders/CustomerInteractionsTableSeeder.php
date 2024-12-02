<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CustomerInteractionsTableSeeder extends Seeder
{
    public function run()
    {  
        DB::table('customer_interactions')->insert([
            ['customer_id' => 1, 'user_id' => 1, 'interaction_type' => 'Call', 'interaction_date' => '2023-06-01 10:00:00', 'notes' => 'Called customer for follow up'],
            ['customer_id' => 2, 'user_id' => 1, 'interaction_type' => 'Email', 'interaction_date' => '2023-06-02 14:30:00', 'notes' => 'Sent quotation to customer'],     
        ]);
    }
}