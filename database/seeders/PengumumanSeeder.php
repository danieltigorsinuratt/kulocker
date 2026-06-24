<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengumumanSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak duplicate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pengumuman')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('pengumuman')->insert([
            [
                'id'         => 1,
                'judul'      => 'Selamat Datang di KuLocker!',
                'isi'        => 'Sistem loker pintar Universitas Mataram kini resmi beroperasi.',
                'kategori'   => 'info',
                'is_aktif'   => 1,
                'created_at' => '2026-06-18 09:51:38',
                'expired_at' => null,
            ],
            [
                'id'         => 2,
                'judul'      => 'Promo Juni 2026',
                'isi'        => 'Perpanjang sewa loker dan dapatkan diskon 20%!',
                'kategori'   => 'promo',
                'is_aktif'   => 1,
                'created_at' => '2026-06-18 09:51:38',
                'expired_at' => null,
            ],
            [
                'id'         => 3,
                'judul'      => 'Maintenance Gedung B',
                'isi'        => 'Loker Gedung B akan maintenance 10 Juni 2026.',
                'kategori'   => 'maintenance',
                'is_aktif'   => 1,
                'created_at' => '2026-06-18 09:51:38',
                'expired_at' => null,
            ],
            [
                'id'         => 4,
                'judul'      => 'Locker Bakal hadir di Fakultas Teknik',
                'isi'        => 'Tunggu kami di fakultas teknik',
                'kategori'   => 'info',
                'is_aktif'   => 1,
                'created_at' => '2026-06-23 01:49:35',
                'expired_at' => null,
            ],
            [
                'id'         => 5,
                'judul'      => 'Locker Bakal hadir di Fakultas Teknik',
                'isi'        => 'Tunggu kami di fakultas teknik',
                'kategori'   => 'info',
                'is_aktif'   => 1,
                'created_at' => '2026-06-23 01:50:05',
                'expired_at' => null,
            ],
        ]);
    }
}
