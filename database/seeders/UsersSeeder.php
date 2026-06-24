<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak duplicate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('users')->insert([
            [
                'id'         => 1,
                'nama'       => 'Admin KuLocker',
                'email'      => 'admin@kulocker.ac.id',
                'password'   => bcrypt('admin123'),   // password: admin123
                'role'       => 'admin',
                'nim'        => null,
                'no_hp'      => '081234567890',
                'alamat'     => '',
                'foto_profil'=> null,
                'created_at' => '2026-06-05 22:52:35',
            ],
            [
                'id'         => 10,
                'nama'       => 'Moh. Saqif Dendi Al Fayyed',
                'email'      => 'dendi0006@gmail.com',
                'password'   => bcrypt('dendi123'),   // password: dendi123
                'role'       => 'mahasiswa',
                'nim'        => 'F1D02410122',
                'no_hp'      => '082148192324',
                'alamat'     => 'Kediri',
                'foto_profil'=> null,
                'created_at' => '2026-06-06 09:02:36',
            ],
            [
                'id'         => 11,
                'nama'       => 'Denduy',
                'email'      => 'nodichannel@gmail.com',
                'password'   => bcrypt('denduy123'),  // password: denduy123
                'role'       => 'mahasiswa',
                'nim'        => 'F1D02410001',
                'no_hp'      => '',
                'alamat'     => '',
                'foto_profil'=> null,
                'created_at' => '2026-06-07 01:30:19',
            ],
            [
                'id'         => 12,
                'nama'       => 'Raka Mbojo',
                'email'      => 'websitekulocker@gmail.com',
                'password'   => bcrypt('raka123'),    // password: raka123
                'role'       => 'mahasiswa',
                'nim'        => 'F1D02410014',
                'no_hp'      => '',
                'alamat'     => '',
                'foto_profil'=> null,
                'created_at' => '2026-06-23 00:54:38',
            ],
        ]);
    }
}

