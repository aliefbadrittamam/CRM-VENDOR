<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ProductsTableSeeder::class,
            UsersTableSeeder::class,
            CustomersTableSeeder::class,
            VendorsTableSeeder::class,
            ProjectsTableSeeder::class, 
            VendorsTableSeeder::class, 
            
        ]);
    }
}