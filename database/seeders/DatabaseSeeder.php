<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan penting! Tabel yang di-reference harus di-seed duluan.
        $this->call([
            UsersSeeder::class,
            LockersSeeder::class,
            PengumumanSeeder::class,
        ]);
    }
}
