<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PasswordController extends Controller
{
    /** GET /forgot-password */
    public function showForgotForm()
    {
        return view('auth.forgot-password', ['success' => false, 'error' => '']);
    }

    /** POST /forgot-password */
    public function sendResetCode(Request $request)
    {
        $conn = DbHelper::connection();
        $email = mysqli_real_escape_string($conn, trim($request->input('email', '')));

        if (empty($email)) {
            return view('auth.forgot-password', ['success' => false, 'error' => 'Email tidak boleh kosong.', 'old_email' => $email]);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return view('auth.forgot-password', ['success' => false, 'error' => 'Format email tidak valid.', 'old_email' => $email]);
        }

        // Cek apakah email terdaftar
        $cek  = mysqli_query($conn, "SELECT id, nama FROM users WHERE email='$email'");
        $user = mysqli_fetch_assoc($cek);

        if (!$user) {
            return view('auth.forgot-password', ['success' => false, 'error' => 'Email ini tidak terdaftar di KuLocker.', 'old_email' => $email]);
        }

        // Generate kode 6 digit
        $kode = rand(100000, 999999);

        // Simpan ke session
        session([
            'reset_email' => $email,
            'reset_kode' => $kode,
            'reset_expired_at' => time() + (10 * 60) // 10 menit
        ]);

        // Kirim email via PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Konfigurasi SMTP Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'websitekulocker@gmail.com';     // ← email Gmail pengirim
            $mail->Password   = 'btco iwhs uxft zhlg';     // ← App Password Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // Pengirim & penerima
            $mail->setFrom('websitekulocker@gmail.com', 'KuLocker');
            $mail->addAddress($email, $user['nama']);

            // Konten email
            $mail->isHTML(true);
            $mail->Subject = 'Kode Verifikasi Reset Password KuLocker';
            $mail->Body    = "
                <div style='font-family: Inter, sans-serif; max-width: 480px; margin: 0 auto; background: #1a1a1a; border-radius: 12px; padding: 32px; color: #fff;'>
                    <h2 style='color: #fbc531; margin-bottom: 8px;'>KuLocker</h2>
                    <p style='color: #888; margin-bottom: 24px;'>Reset Password</p>
                    <p style='margin-bottom: 16px;'>Halo <strong>{$user['nama']}</strong>,</p>
                    <p style='margin-bottom: 24px; color: #ccc;'>Gunakan kode berikut untuk mereset password kamu:</p>
                    <div style='background: #111; border: 1px solid #333; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 24px;'>
                        <span style='font-size: 36px; font-weight: 800; letter-spacing: 12px; color: #fbc531;'>{$kode}</span>
                    </div>
                    <p style='color: #888; font-size: 13px;'>Kode berlaku selama <strong style='color:#fbc531;'>10 menit</strong>. Jangan bagikan ke siapapun.</p>
                    <hr style='border-color: #2a2a2a; margin: 24px 0;'>
                    <p style='color: #555; font-size: 12px;'>Jika kamu tidak meminta reset password, abaikan email ini.</p>
                </div>
            ";
            $mail->AltBody = "Kode reset password KuLocker kamu: $kode. Berlaku 10 menit.";

            $mail->send();
            return view('auth.forgot-password', ['success' => true, 'error' => '']);

        } catch (Exception $e) {
            return view('auth.forgot-password', ['success' => false, 'error' => 'Mailer Error: ' . $mail->ErrorInfo, 'old_email' => $email]);
        }
    }

    /** GET /verify-code */
    public function showVerifyForm()
    {
        if (!session()->has('reset_email') || !session()->has('reset_kode')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-code', ['error' => '']);
    }

    /** POST /verify-code */
    public function verifyCode(Request $request)
    {
        if (!session()->has('reset_email') || !session()->has('reset_kode')) {
            return redirect()->route('password.request');
        }

        // Gabungkan 6 digit dari input terpisah
        $kode = '';
        for ($i = 1; $i <= 6; $i++) {
            $kode .= $request->input('kode_' . $i, '');
        }

        if (strlen($kode) !== 6 || !ctype_digit($kode)) {
            return view('auth.verify-code', ['error' => 'Kode verifikasi harus 6 digit angka.']);
        } elseif (time() > session('reset_expired_at')) {
            return view('auth.verify-code', ['error' => 'Kode sudah kadaluarsa. Silakan kirim ulang.']);
        } elseif ($kode !== (string) session('reset_kode')) {
            return view('auth.verify-code', ['error' => 'Kode verifikasi tidak cocok. Periksa kembali email kamu.']);
        }

        // Kode benar → tandai verified, redirect ke reset-password
        session(['reset_verified' => true]);
        return redirect()->route('password.reset');
    }

    /** GET /reset-password */
    public function showResetForm()
    {
        if (!session('reset_verified') || !session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password', ['success' => false, 'error' => '']);
    }

    /** POST /reset-password */
    public function resetPassword(Request $request)
    {
        if (!session('reset_verified') || !session('reset_email')) {
            return redirect()->route('password.request');
        }

        $email = session('reset_email');
        $new_password     = $request->input('new_password', '');
        $confirm_password = $request->input('confirm_password', '');

        if (strlen($new_password) < 8) {
            return view('auth.reset-password', ['success' => false, 'error' => 'Password minimal 8 karakter.']);
        } elseif (!preg_match('/[0-9]/', $new_password)) {
            return view('auth.reset-password', ['success' => false, 'error' => 'Password harus mengandung angka.']);
        } elseif (!preg_match('/[^A-Za-z0-9]/', $new_password)) {
            return view('auth.reset-password', ['success' => false, 'error' => 'Password harus mengandung simbol.']);
        } elseif ($new_password !== $confirm_password) {
            return view('auth.reset-password', ['success' => false, 'error' => 'Konfirmasi password tidak cocok.']);
        }

        $conn = DbHelper::connection();
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $email_db = mysqli_real_escape_string($conn, $email);

        $sql = "UPDATE users SET password='$hashed' WHERE email='$email_db'";

        if (mysqli_query($conn, $sql)) {
            // Bersihkan semua session reset
            session()->forget(['reset_email', 'reset_kode', 'reset_expired_at', 'reset_verified']);
            return view('auth.reset-password', ['success' => true, 'error' => '']);
        } else {
            return view('auth.reset-password', ['success' => false, 'error' => 'Gagal memperbarui password. Coba lagi.']);
        }
    }
}
