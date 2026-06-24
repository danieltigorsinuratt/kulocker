<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LockersSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak duplicate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('lockers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('lockers')->insert([
            ['id' => 1, 'kode_loker' => 'A-01', 'lokasi' => 'Gedung A Lt.1', 'ukuran' => 'S', 'status' => 'tersedia', 'created_at' => '2026-06-05 22:52:35', 'is_deleted' => 0],
            ['id' => 2, 'kode_loker' => 'A-02', 'lokasi' => 'Gedung A Lt.1', 'ukuran' => 'S', 'status' => 'tersedia', 'created_at' => '2026-06-05 22:52:35', 'is_deleted' => 0],
            ['id' => 3, 'kode_loker' => 'A-03', 'lokasi' => 'Gedung A Lt.1', 'ukuran' => 'S', 'status' => 'tersedia', 'created_at' => '2026-06-05 22:52:35', 'is_deleted' => 0],
            ['id' => 4, 'kode_loker' => 'B-01', 'lokasi' => 'Gedung B Lt.2', 'ukuran' => 'S', 'status' => 'rusak',    'created_at' => '2026-06-05 22:52:35', 'is_deleted' => 0],
            ['id' => 5, 'kode_loker' => 'B-02', 'lokasi' => 'Gedung B Lt.2', 'ukuran' => 'S', 'status' => 'rusak',    'created_at' => '2026-06-05 22:52:35', 'is_deleted' => 0],
            ['id' => 6, 'kode_loker' => 'B-03', 'lokasi' => 'Gedung B Lt.2', 'ukuran' => 'S', 'status' => 'tersedia', 'created_at' => '2026-06-05 22:52:35', 'is_deleted' => 0],
            ['id' => 8, 'kode_loker' => 'A-04', 'lokasi' => 'Gedung A Lt.1', 'ukuran' => 'S', 'status' => 'tersedia', 'created_at' => '2026-06-23 01:33:32', 'is_deleted' => 0],
        ]);
    }
}
