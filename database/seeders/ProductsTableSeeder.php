<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        // Membuat instance Faker
        $faker = Faker::create();

        // Menambahkan beberapa data produk secara acak
        for ($i = 0; $i < 10; $i++) {
            DB::table('products')->insert([
                'product_name' => $faker->word, // Nama produk acak
                'product_category' => $faker->word, // Kategori produk acak
                'product_price' => $faker->randomFloat(2, 10000, 1000000), // Harga produk acak antara 10.000 hingga 1.000.000
                'description' => $faker->paragraph, // Deskripsi acak
                'created_at' => now(), // Tanggal saat ini
                'updated_at' => now(), // Tanggal saat ini
            ]);
        }
    }
}
