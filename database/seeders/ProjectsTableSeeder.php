<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create(); // Membuat instance Faker

        DB::table('projects')->insert([
            [
                'vendor_id' => 6, // Mengacu pada tabel `vendors`
                'customer_id' => 1, // Mengacu pada tabel `customers`
                'product_id' => 1, // Mengacu pada tabel `products`
                'project_header' => $faker->word, // Menghasilkan judul proyek acak
                'project_value' => $faker->numberBetween(1000000, 5000000), // Nilai proyek acak
                'project_duration_start' => $faker->dateTimeThisYear, // Tanggal mulai acak dalam tahun ini
                'project_duration_end' => $faker->dateTimeThisYear->modify('+3 months'), // Tanggal akhir 3 bulan setelah start date
                'project_detail' => $faker->text(200), // Deskripsi proyek acak
            ],
            [
                'vendor_id' => 4, // Mengacu pada tabel `vendors`
                'customer_id' => 2, // Mengacu pada tabel `customers`
                'product_id' => 2, // Mengacu pada tabel `products`
                'project_header' => $faker->word, // Menghasilkan judul proyek acak
                'project_value' => $faker->numberBetween(2000000, 6000000), // Nilai proyek acak
                'project_duration_start' => $faker->dateTimeThisYear, // Tanggal mulai acak dalam tahun ini
                'project_duration_end' => $faker->dateTimeThisYear->modify('+6 months'), // Tanggal akhir 6 bulan setelah start date
                'project_detail' => $faker->text(200), // Deskripsi proyek acak
            ],
        ]);
    }
}
