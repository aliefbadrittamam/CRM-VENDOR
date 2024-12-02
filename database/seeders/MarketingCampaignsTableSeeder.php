<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class MarketingCampaignsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('marketing_campaigns')->insert([
            ['campaign_name' => 'Campaign A', 'start_date' => '2023-06-01', 'end_date' => '2023-06-30', 'description' => 'Description of Campaign A'],
            ['campaign_name' => 'Campaign B', 'start_date' => '2023-07-01', 'end_date' => '2023-07-31', 'description' => 'Description of Campaign B'],
        ]);
    }
}

