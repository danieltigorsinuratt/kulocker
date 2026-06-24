<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /** GET /admin */
    public function index(Request $request)
    {
        $conn = DbHelper::connection();
        $user = session('user');
        $admin_nama = $user['nama'] ?? 'Admin';

        // Generate inisial huruf untuk avatar secara otomatis
        $kata = explode(" ", $admin_nama);
        $admin_avatar = strtoupper(substr($kata[0], 0, 1)) . (isset($kata[1]) ? strtoupper(substr($kata[1], 0, 1)) : "");

        $page = $request->get('page', 'dashboard');

        return view('admin', compact('page', 'admin_nama', 'admin_avatar', 'conn'));
    }

    /** POST /admin */
    public function handlePost(Request $request)
    {
        $conn = DbHelper::connection();
        $action = $request->input('action');

        if ($action === 'hapus_locker') {
            $locker_id = intval($request->input('locker_id', 0));
            if ($locker_id > 0) {
                // Soft-delete the locker so related pemesanan history remains intact
                mysqli_query($conn, "UPDATE lockers SET is_deleted = 1 WHERE id = $locker_id");
                return redirect()->to(route('admin', ['page' => 'locker', 'msg' => 'hapus_berhasil']));
            }
            return redirect()->to(route('admin', ['page' => 'locker', 'msg' => 'hapus_gagal']));
        }

        return redirect()->route('admin');
    }

    /** GET /admin/tambah-locker */
    public function showTambahLocker()
    {
        return view('admin_tambah_locker', ['msg' => '']);
    }

    /** POST /admin/tambah-locker */
    public function tambahLockerPost(Request $request)
    {
        $conn = DbHelper::connection();
        $kode = trim($request->input('kode_loker', ''));
        $lokasi = trim($request->input('lokasi', ''));
        $ukuran = trim($request->input('ukuran', ''));

        if ($kode !== '' && $lokasi !== '' && in_array($ukuran, ['S','M','L'], true)) {
            $kode_s = mysqli_real_escape_string($conn, $kode);
            $lokasi_s = mysqli_real_escape_string($conn, $lokasi);
            $ukuran_s = mysqli_real_escape_string($conn, $ukuran);

            $cek_aktif = mysqli_query($conn, "SELECT id FROM lockers WHERE kode_loker = '$kode_s' AND (is_deleted = 0 OR is_deleted IS NULL)");
            if (mysqli_num_rows($cek_aktif) === 0) {
                $cek_hapus = mysqli_query($conn, "SELECT id FROM lockers WHERE kode_loker = '$kode_s' AND is_deleted = 1 ORDER BY id DESC LIMIT 1");
                if (mysqli_num_rows($cek_hapus) > 0) {
                    $hapus_row = mysqli_fetch_assoc($cek_hapus);
                    $restore_id = intval($hapus_row['id']);
                    mysqli_query($conn, "UPDATE lockers SET lokasi = '$lokasi_s', ukuran = '$ukuran_s', status = 'Tersedia', is_deleted = 0 WHERE id = $restore_id");
                } else {
                    mysqli_query($conn, "INSERT INTO lockers (kode_loker, lokasi, ukuran, status, is_deleted, created_at) VALUES ('$kode_s', '$lokasi_s', '$ukuran_s', 'Tersedia', 0, NOW())");
                }
                return redirect()->to(route('admin', ['page' => 'locker', 'msg' => 'tambah_berhasil']));
            } else {
                return view('admin_tambah_locker', ['msg' => 'exists']);
            }
        } else {
            return view('admin_tambah_locker', ['msg' => 'invalid']);
        }
    }

    /** GET /admin/update-status */
    public function updateStatus(Request $request)
    {
        $conn = DbHelper::connection();
        $id = intval($request->input('id', 0));
        $status = mysqli_real_escape_string($conn, $request->input('status', ''));

        if ($id > 0 && !empty($status)) {
            $query = "UPDATE lockers SET status = '$status' WHERE id = $id";
            if (mysqli_query($conn, $query)) {
                return response('OK', 200);
            } else {
                return response('Error', 500);
            }
        }

        return response('Bad Request', 400);
    }
}
