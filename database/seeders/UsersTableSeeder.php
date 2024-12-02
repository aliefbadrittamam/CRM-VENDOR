<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Membuat instance Faker

        DB::table('users')->insert([
            [
                'name' => $faker->name, // Nama acak
                'email' => $faker->unique()->safeEmail, // Email acak yang aman dan unik
                'password' => bcrypt('password'), // Pastikan password di-hash
                'role' => 'Admin',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => $faker->name, // Nama acak
                'email' => $faker->unique()->safeEmail, // Email acak yang aman dan unik
                'password' => bcrypt('customer'),
                'role' => 'Customers',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => $faker->name, // Nama acak
                'email' => $faker->unique()->safeEmail, // Email acak yang aman dan unik
                'password' => bcrypt('vendor'),
                'role' => 'Vendor',
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
