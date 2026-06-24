<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class LockerController extends Controller
{
    /** GET /locker-selection?lokasi=xxx */
    public function selection(Request $request)
    {
        $conn = DbHelper::connection();

        if (!$request->has('lokasi') || empty($request->query('lokasi'))) {
            return redirect()->route('dashboard');
        }

        $lokasi_terpilih = $request->query('lokasi');

        $lockers_list = [];
        $query = "SELECT l.id, l.kode_loker, l.ukuran, l.status, p.tanggal_selesai
                  FROM lockers l
                  LEFT JOIN pemesanan p ON l.id = p.locker_id AND p.status = 'aktif'
                  WHERE l.lokasi = ? AND (l.is_deleted = 0 OR l.is_deleted IS NULL)
                  ORDER BY l.kode_loker ASC";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $lokasi_terpilih);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $lockers_list[] = $row;
            }
        }

        return view('locker.selection', compact('lokasi_terpilih', 'lockers_list'));
    }

    /** POST /api/proses-sewa - Proses sewa loker */
    public function sewa(Request $request)
    {
        date_default_timezone_set('Asia/Makassar');
        $conn = DbHelper::connection();

        $locker_id  = intval($request->input('locker_id', 0));
        $durasi_jam = intval($request->input('durasi_jam', 0));
        $user_id    = intval(session('user.id', 10));

        if ($durasi_jam < 1 || $durasi_jam > 3 || $locker_id === 0) {
            return redirect()->route('dashboard')->with('error', 'Durasi atau loker tidak valid.');
        }

        $sekarang        = time();
        $tanggal_mulai   = date('Y-m-d H:i:s', $sekarang);
        $tanggal_selesai = date('Y-m-d H:i:s', $sekarang + ($durasi_jam * 3600));
        $kode_akses      = strval(rand(100000, 999999));

        $query_order = "INSERT INTO pemesanan (user_id, locker_id, tanggal_mulai, tanggal_selesai, status, kode_akses) VALUES (?, ?, ?, ?, 'aktif', ?)";
        $stmt_order  = mysqli_prepare($conn, $query_order);
        mysqli_stmt_bind_param($stmt_order, "iisss", $user_id, $locker_id, $tanggal_mulai, $tanggal_selesai, $kode_akses);
        mysqli_stmt_execute($stmt_order);
        $pemesanan_id = mysqli_insert_id($conn);

        $query_pay  = "INSERT INTO pembayaran (pemesanan_id, jumlah, metode, status, bukti) VALUES (?, 0, 'gratis', 'lunas', NULL)";
        $stmt_pay   = mysqli_prepare($conn, $query_pay);
        mysqli_stmt_bind_param($stmt_pay, "i", $pemesanan_id);
        mysqli_stmt_execute($stmt_pay);

        $query_upd = "UPDATE lockers SET status = 'terpakai' WHERE id = ?";
        $stmt_lkr  = mysqli_prepare($conn, $query_upd);
        mysqli_stmt_bind_param($stmt_lkr, "i", $locker_id);
        mysqli_stmt_execute($stmt_lkr);

        // Simpan di session sementara untuk redirect ke halaman sukses
        session(['pending_pemesanan_id' => $pemesanan_id]);

        return view('locker.loading', compact('pemesanan_id'));
    }

    /** GET /proses-sukses */
    public function prosesSukses(Request $request)
    {
        $conn = DbHelper::connection();
        $pemesanan_id = intval($request->input('pemesanan_id', session('pending_pemesanan_id', 0)));

        if ($pemesanan_id <= 0) {
            return redirect()->route('dashboard');
        }

        $query_select = "SELECT p.tanggal_mulai, p.kode_akses, l.kode_loker, l.lokasi
                          FROM pemesanan p
                          JOIN lockers l ON p.locker_id = l.id
                          WHERE p.id = ?";
        $stmt = mysqli_prepare($conn, $query_select);
        mysqli_stmt_bind_param($stmt, "i", $pemesanan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data_sewa = mysqli_fetch_assoc($result);

        if (!$data_sewa) {
            return redirect()->route('dashboard');
        }

        $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($data_sewa['kode_akses']);

        session()->forget('pending_pemesanan_id');

        return view('locker.sukses', compact('pemesanan_id', 'data_sewa', 'qr_code_url'));
    }

    /** GET /tiket-saya */
    public function tiket()
    {
        date_default_timezone_set('Asia/Makassar');
        $conn    = DbHelper::connection();
        $user_id = intval(session('user.id', 10));

        $daftar_tiket = [];
        $query = "SELECT p.id AS pemesanan_id, p.tanggal_selesai, p.kode_akses, l.kode_loker, l.lokasi
                   FROM pemesanan p
                   JOIN lockers l ON p.locker_id = l.id
                   WHERE p.user_id = ? AND p.status = 'aktif'
                   ORDER BY p.id DESC";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['target_timestamp'] = strtotime($row['tanggal_selesai']) * 1000;
                $daftar_tiket[] = $row;
            }
        }

        return view('locker.tiket', compact('daftar_tiket'));
    }

    /** POST /tiket/selesai */
    public function selesaiSewa(Request $request)
    {
        $conn        = DbHelper::connection();
        $user_id     = intval(session('user.id', 10));
        $pemesanan_id = intval($request->input('pemesanan_id', 0));

        if ($pemesanan_id > 0) {
            mysqli_begin_transaction($conn);
            try {
                $stmt_p = mysqli_prepare($conn, "UPDATE pemesanan SET status = 'selesai' WHERE id = ? AND user_id = ?");
                mysqli_stmt_bind_param($stmt_p, "ii", $pemesanan_id, $user_id);
                mysqli_stmt_execute($stmt_p);

                $stmt_g = mysqli_prepare($conn, "SELECT locker_id FROM pemesanan WHERE id = ?");
                mysqli_stmt_bind_param($stmt_g, "i", $pemesanan_id);
                mysqli_stmt_execute($stmt_g);
                $res   = mysqli_stmt_get_result($stmt_g);
                $data  = mysqli_fetch_assoc($res);

                if ($data) {
                    $locker_id = $data['locker_id'];
                    $stmt_l = mysqli_prepare($conn, "UPDATE lockers SET status = 'tersedia' WHERE id = ?");
                    mysqli_stmt_bind_param($stmt_l, "i", $locker_id);
                    mysqli_stmt_execute($stmt_l);
                }

                mysqli_commit($conn);
            } catch (\Exception $e) {
                mysqli_rollback($conn);
            }
        }

        return redirect()->route('tiket')->with('msg', 'sukses_selesai');
    }

    /** GET /riwayat-sewa */
    public function riwayat()
    {
        $conn    = DbHelper::connection();
        $user    = session('user');
        $user_id = $user['id'];

        $riwayat_list = [];
        $query = "SELECT p.*, l.kode_loker, l.lokasi, l.ukuran
                   FROM pemesanan p
                   JOIN lockers l ON p.locker_id = l.id
                   WHERE p.user_id = '$user_id'
                     AND p.status IN ('selesai', 'dibatalkan')
                   ORDER BY p.id DESC";

        $result = mysqli_query($conn, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $riwayat_list[] = $row;
            }
        }

        return view('locker.riwayat', compact('riwayat_list'));
    }

    /** GET /api/cron-pengingat */
    public function cron()
    {
        $conn = DbHelper::connection();
        date_default_timezone_set('Asia/Makassar');
        mysqli_query($conn, "SET time_zone = '+08:00'");

        $query = "SELECT p.id AS pemesanan_id, p.tanggal_selesai, p.notifikasi_step, 
                          l.id AS locker_id, l.kode_loker, l.lokasi, u.nama, u.email 
                  FROM pemesanan p
                  JOIN lockers l ON p.locker_id = l.id
                  JOIN users u ON p.user_id = u.id
                  WHERE p.status = 'aktif'";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $pemesanan_id = $row['pemesanan_id'];
                $locker_id = $row['locker_id'];
                $email_user = $row['email'];
                $nama_user = $row['nama'];
                $kode_loker = $row['kode_loker'];
                $lokasi_loker = $row['lokasi'];
                $step_sekarang = $row['notifikasi_step'];

                $waktu_sekarang = time();
                $waktu_selesai = strtotime($row['tanggal_selesai']);
                $sisa_menit = ceil(($waktu_selesai - $waktu_sekarang) / 60);

                $kirim_email = false;
                $subject = "";
                $body_content = "";
                $next_step = "";
                $update_status_order = false; 

                if ($sisa_menit <= 0 && $step_sekarang !== 'terkunci') {
                    $kirim_email = true;
                    $next_step = 'terkunci';
                    $update_status_order = true; 
                    
                    $subject = "⚠️ PENTING: Waktu Habis, Loker {$kode_loker} Telah Terkunci!";
                    $body_content = "
                        <h2 style='color: #ef4444;'>Waktu Penggunaan Habis!</h2>
                        <p>Halo <b>{$nama_user}</b>, masa sewa kamu untuk loker <b>{$kode_loker}</b> di {$lokasi_loker} telah berakhir.</p>
                        <div style='background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px; margin: 15px 0;'>
                            <p style='margin: 0; font-weight: bold; color: #991b1b;'>Status: TERKUNCI KARENA TELAT</p>
                            <p style='margin: 5px 0 0 0; font-size: 0.9rem;'>Sistem telah mengunci akses loker kamu secara otomatis. Silakan hubungi <b>Bagian Admin Utama atau Satpam Penjaga Gedung</b> untuk melakukan pembukaan manual dan pengambilan barang kamu.</p>
                        </div>
                    ";
                } elseif ($sisa_menit <= 1 && $sisa_menit > 0 && $step_sekarang === '5_menit') {
                    $kirim_email = true;
                    $next_step = '1_menit';
                    $subject = "🚨 KRITIS: 1 Menit Lagi Loker {$kode_loker} Terkunci!";
                    $body_content = "
                        <h2 style='color: #dc2626;'>Peringatan Terakhir (1 Menit Lagi)!</h2>
                        <p>Halo <b>{$nama_user}</b>, waktu kamu tersisa kurang dari <b>1 menit</b> lagi. Tolong segera kosongkan loker <b>{$kode_loker}</b> sekarang juga sebelum sistem mengunci barang-barang kamu secara permanen!</p>
                    ";
                } elseif ($sisa_menit <= 5 && $sisa_menit > 1 && $step_sekarang === '15_menit') {
                    $kirim_email = true;
                    $next_step = '5_menit';
                    $subject = "⚠️ Peringatan: 5 Menit Lagi Loker {$kode_loker} Terkunci";
                    $body_content = "
                        <h2 style='color: #f59e0b;'>Waktu Tersisa 5 Menit!</h2>
                        <p>Halo <b>{$nama_user}</b>, diingatkan kembali bahwa sisa waktu penggunaan loker <b>{$kode_loker}</b> tersisa <b>5 menit</b> lagi. Mohon segera menuju lokasi loker untuk mengosongkan tempat.</p>
                    ";
                } elseif ($sisa_menit <= 15 && $sisa_menit > 5 && $step_sekarang === 'belum') {
                    $kirim_email = true;
                    $next_step = '15_menit';
                    $subject = "Pemberitahuan: 15 Menit Sebelum Waktu Sewa Loker {$kode_loker} Habis";
                    $body_content = "
                        <h2 style='color: #eab308;'>Pengingat Waktu (15 Menit)</h2>
                        <p>Halo <b>{$nama_user}</b>, kami ingin menginformasikan bahwa durasi penggunaan loker <b>{$kode_loker}</b> di {$lokasi_loker} tersisa <b>15 menit</b> lagi.</p>
                        <p>Harap bersiap-siap untuk mengosongkan loker tepat waktu karena sistem kami tidak mendukung perpanjangan otomatis.</p>
                    ";
                }

                if ($kirim_email) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'websitekulocker@gmail.com';     
                        $mail->Password   = 'btco iwhs uxft zhlg';     
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('kulocker.unram@gmail.com', 'KuLocker Universitas Mataram');
                        $mail->addAddress($email_user, $nama_user);

                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body    = "
                            <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px;'>
                                {$body_content}
                                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                                <p style='font-size: 0.85rem; color: #94a3b8;'>© 2026 KuLocker Unram. Sistem Otomatis Pemberitahuan Kampus.</p>
                            </div>
                        ";

                        $mail->send();

                        if ($update_status_order) {
                            mysqli_query($conn, "UPDATE pemesanan SET status = 'selesai', notifikasi_step = '$next_step' WHERE id = $pemesanan_id");
                            mysqli_query($conn, "UPDATE lockers SET status = 'rusak' WHERE id = $locker_id");
                            echo "Loker {$kode_loker} SUDAH HABIS WAKTUNYA. Status diubah menjadi RUSAK & Email Kunci Terkirim.<br>";
                        } else {
                            mysqli_query($conn, "UPDATE pemesanan SET notifikasi_step = '$next_step' WHERE id = $pemesanan_id");
                            echo "Email pengingat Tahap [{$next_step}] sukses dikirim ke {$email_user}. Sisa: {$sisa_menit} Menit.<br>";
                        }

                    } catch (Exception $e) {
                        echo "Gagal memproses email ke {$email_user}. Error: {$mail->ErrorInfo}<br>";
                    }
                } else {
                    echo "User {$nama_user} (Loker {$kode_loker}) dilewati. Sisa: {$sisa_menit} Menit. Status step saat ini: [{$step_sekarang}].<br>";
                }
            }
        } else {
            echo "Tidak ada loker aktif yang perlu diproses saat ini.";
        }
    }
}
