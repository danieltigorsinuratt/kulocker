<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    /** GET /pengumuman */
    public function index(Request $request)
    {
        $conn = DbHelper::connection();
        $user = session('user');

        if (!$user) {
            return redirect()->route('sign-in');
        }

        // Filter kategori
        $kategori_filter = $request->get('kategori', 'semua');
        $allowed = ['semua', 'info', 'promo', 'peringatan', 'maintenance'];
        if (!in_array($kategori_filter, $allowed)) {
            $kategori_filter = 'semua';
        }

        // Pagination
        $per_page = 9;
        $page     = max(1, (int) ($request->get('page', 1)));
        $offset   = ($page - 1) * $per_page;

        // Query total
        $where = "WHERE is_aktif = 1 AND (expired_at IS NULL OR expired_at > NOW())";
        if ($kategori_filter !== 'semua') {
            $kat = mysqli_real_escape_string($conn, $kategori_filter);
            $where .= " AND kategori = '$kat'";
        }

        $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman $where");
        $total = 0;
        if ($totalQuery) {
            $total = mysqli_fetch_assoc($totalQuery)['total'];
        }
        $total_page = ceil($total / $per_page);

        // Query data
        $result = mysqli_query($conn, "SELECT * FROM pengumuman $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
        $pengumuman_list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $pengumuman_list[] = $row;
            }
        }

        return view('pengumuman', compact('user', 'pengumuman_list', 'kategori_filter', 'total', 'total_page', 'page'));
    }
}
