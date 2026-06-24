<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;

class DashboardController extends Controller
{
    /** GET / - Landing page publik (dashboard.php) */
    public function landing()
    {
        return view('dashboard.landing');
    }

    /** GET /dashboard - Dashboard user login (dashboard-utama.php) */
    public function main()
    {
        $conn = DbHelper::connection();
        $user = session('user');

        // Ambil foto profil terbaru jika tidak ada di session
        if (empty($user['foto_profil']) && !empty($user['id'])) {
            $id = intval($user['id']);
            $r  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto_profil FROM users WHERE id = $id LIMIT 1"));
            if ($r && !empty($r['foto_profil'])) {
                $user['foto_profil'] = $r['foto_profil'];
            }
        }

        // Data lokasi loker
        $lokasi_list = [];
        $res_lok = mysqli_query($conn, "
            SELECT lokasi,
                   COUNT(*) AS total,
                   SUM(status = 'tersedia') AS tersedia
            FROM lockers
            WHERE (is_deleted = 0 OR is_deleted IS NULL)
            GROUP BY lokasi
            ORDER BY lokasi ASC
        ");
        if ($res_lok) {
            while ($row = mysqli_fetch_assoc($res_lok)) {
                $lokasi_list[] = $row;
            }
        }

        // Pengumuman
        $pengumuman_list = [];
        $res_p = mysqli_query($conn, "SELECT * FROM pengumuman LIMIT 6");
        if ($res_p) {
            while ($row = mysqli_fetch_assoc($res_p)) {
                $pengumuman_list[] = $row;
            }
        }

        return view('dashboard.main', compact('user', 'lokasi_list', 'pengumuman_list'));
    }
}
