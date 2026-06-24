<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /** GET /sign-in */
    public function showLogin()
    {
        if (session()->has('user')) {
            return redirect()->route('dashboard');
        }

        $error = session()->pull('error', '');
        return view('auth.sign-in', compact('error'));
    }

    /** POST /auth/login */
    public function login(Request $request)
    {
        $conn = DbHelper::connection();
        $nim  = $request->input('nim', '');
        $password = $request->input('password', '');

        // Login hardcoded admin
        if ($nim === 'admin' && $password === 'admin') {
            session(['user' => [
                'id'    => 'admin',
                'nama'  => 'Administrator Utama',
                'email' => 'admin@domain.com',
                'nim'   => 'admin',
                'no_hp' => '-',
                'role'  => 'admin',
            ]]);
            return redirect()->route('admin');
        }

        $nim_safe = mysqli_real_escape_string($conn, $nim);
        $query = mysqli_query($conn, "SELECT * FROM users WHERE nim = '$nim_safe' LIMIT 1");

        if (mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);

            if (password_verify($password, $user['password'])) {
                session(['user' => [
                    'id'    => $user['id'],
                    'nama'  => $user['nama'],
                    'email' => $user['email'],
                    'nim'   => $user['nim'],
                    'no_hp' => $user['no_hp'],
                    'role'  => $user['role'],
                ]]);

                if ($user['role'] === 'admin') {
                    return redirect()->route('admin');
                }
                return redirect()->route('dashboard');
            } else {
                session(['error' => 'Password yang Anda masukkan salah!']);
                return redirect()->route('sign-in');
            }
        } else {
            session(['error' => 'NIM tidak ditemukan atau belum terdaftar!']);
            return redirect()->route('sign-in');
        }
    }

    /** GET /sign-up */
    public function showRegister()
    {
        return view('auth.sign-up');
    }

    /** POST /auth/register */
    public function register(Request $request)
    {
        $conn = DbHelper::connection();

        $nama             = mysqli_real_escape_string($conn, trim($request->input('nama', '')));
        $nim              = mysqli_real_escape_string($conn, trim($request->input('nim', '')));
        $email            = mysqli_real_escape_string($conn, trim($request->input('email', '')));
        $password         = $request->input('password', '');
        $confirm_password = $request->input('confirm_password', '');

        if (empty($password) || empty($confirm_password)) {
            return back()->with('error', 'Kata sandi tidak boleh kosong!');
        }
        if (strlen($password) < 8) {
            return back()->with('error', 'Kata sandi minimal 8 karakter!');
        }
        if ($password !== $confirm_password) {
            return back()->with('error', 'Maaf, konfirmasi kata sandi tidak cocok!');
        }

        $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' OR nim='$nim'");
        if (mysqli_num_rows($cek) > 0) {
            return back()->with('error', 'Maaf, NIM atau Email ini sudah terdaftar!');
        }

        $no_hp = mysqli_real_escape_string($conn, trim($request->input('no_hp', '')));
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (nama, nim, email, no_hp, password) VALUES ('$nama', '$nim', '$email', '$no_hp', '$password_hashed')";

        if (mysqli_query($conn, $sql)) {
            session(['nama' => $nama, 'email' => $email]);
            return redirect()->route('konfirmasi')->with('register', 'sukses');
        } else {
            return back()->with('error', 'Gagal mendaftar: ' . mysqli_error($conn));
        }
    }

    /** POST /auth/logout */
    public function logout()
    {
        session()->forget('user');
        session()->flush();
        return redirect()->route('landing');
    }

    /** GET /konfirmasi */
    public function konfirmasi()
    {
        return view('auth.konfirmasi');
    }
}
