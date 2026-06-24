<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;
use Illuminate\Http\Request;

class KeluhanController extends Controller
{
    /** GET /keluhan */
    public function index()
    {
        $conn = DbHelper::connection();
        $user = session('user');
        
        $id_users = is_array($user) 
            ? ($user['id'] ?? $user['id_users'] ?? null) 
            : $user;

        if (!$id_users) {
            return redirect()->route('login');
        }

        // Ambil data user saat ini untuk auto-fill nama dan email di form
        $stmtUser = mysqli_prepare($conn, "SELECT nama, email FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtUser, "i", $id_users);
        mysqli_stmt_execute($stmtUser);
        $resUser = mysqli_stmt_get_result($stmtUser);
        $user_data = mysqli_fetch_assoc($resUser) ?: ['nama' => '', 'email' => ''];

        // Ambil daftar riwayat pemesanan milik user untuk dihubungkan ke keluhan (Opsional di form)
        $query_sewa = "SELECT p.id AS pemesanan_id, l.id AS locker_id, l.kode_loker, l.lokasi, p.status 
                       FROM pemesanan p 
                       JOIN lockers l ON p.locker_id = l.id 
                       WHERE p.user_id = ? 
                       ORDER BY p.created_at DESC";
        $stmtSewa = mysqli_prepare($conn, $query_sewa);
        mysqli_stmt_bind_param($stmtSewa, "i", $id_users);
        mysqli_stmt_execute($stmtSewa);
        $result_sewa = mysqli_stmt_get_result($stmtSewa);

        $daftar_sewa = [];
        while ($row = mysqli_fetch_assoc($result_sewa)) {
            $daftar_sewa[] = $row;
        }

        return view('keluhan', compact('user_data', 'daftar_sewa'));
    }

    /** POST /keluhan */
    public function submit(Request $request)
    {
        $conn = DbHelper::connection();
        $user = session('user');
        
        $id_users = is_array($user) 
            ? ($user['id'] ?? $user['id_users'] ?? null) 
            : $user;

        if (!$id_users) {
            return redirect()->route('login');
        }

        $fullName = htmlspecialchars($request->input('fullName'));
        $email    = htmlspecialchars($request->input('email'));
        $category = htmlspecialchars($request->input('category'));
        $details  = htmlspecialchars($request->input('details'));
        
        $sewa_terpilih = $request->input('pemesanan_loker', '');
        $locker_id = null;
        $pemesanan_id = null;

        if (!empty($sewa_terpilih)) {
            list($p_id, $l_id) = explode('_', $sewa_terpilih);
            $pemesanan_id = (int)$p_id;
            $locker_id = (int)$l_id;
        }

        $judul_keluhan = "";
        switch($category) {
            case 'product': $judul_keluhan = "Loker rusak"; break;
            case 'delivery': $judul_keluhan = "Loker tidak bisa dibuka"; break;
            case 'service': $judul_keluhan = "Pelayanan Tidak Memuaskan"; break;
            case 'website': $judul_keluhan = "Kendala Website / Aplikasi"; break;
            case 'other': $judul_keluhan = "Lainnya"; break;
            default: $judul_keluhan = "Keluhan Umum"; break;
        }

        $sql_insert = "INSERT INTO keluhan (user_id, locker_id, pemesanan_id, judul, deskripsi, status) VALUES (?, ?, ?, ?, ?, 'open')";
        $stmtInsert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmtInsert, "iiiss", $id_users, $locker_id, $pemesanan_id, $judul_keluhan, $details);
        $eksekusi = mysqli_stmt_execute($stmtInsert);

        if ($eksekusi) {
            return view('keluhan_sukses', compact('fullName', 'email', 'judul_keluhan', 'details'));
        } else {
            return back()->with('error', 'Gagal mengirim keluhan. Silakan coba lagi.');
        }
    }
}
