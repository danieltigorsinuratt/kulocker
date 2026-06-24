<?php

namespace App\Http\Controllers;

use App\Helpers\DbHelper;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private function getId(): int
    {
        $user = session('user');
        return intval(is_array($user) ? ($user['id'] ?? $user['id_users'] ?? 0) : $user);
    }

    private function getProfil(int $id): array
    {
        $conn = DbHelper::connection();
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: [];
    }

    /** GET /profile */
    public function index(Request $request)
    {
        $id_users = $this->getId();
        if (!$id_users) return redirect()->route('sign-in');

        $flash = session()->pull('flash');
        $stay_edit = false;
        $tab = $request->query('tab', 'profil');
        $profil = $this->getProfil($id_users);

        return view('profile', compact('profil', 'flash', 'stay_edit', 'tab', 'id_users'));
    }

    /** POST /profile/update */
    public function update(Request $request)
    {
        $conn = DbHelper::connection();
        $id_users = $this->getId();
        if (!$id_users) return redirect()->route('sign-in');

        $nama   = $request->input('nama', '');
        $nim    = $request->input('nim', '');
        $alamat = $request->input('alamat', '');
        $email  = $request->input('email', '');
        $no_hp  = $request->input('no_hp', '');
        $err    = [];

        if (!$nama) $err[] = "Nama wajib diisi.";
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err[] = "Email tidak valid.";

        $foto = null;
        if ($request->hasFile('foto_baru') && $request->file('foto_baru')->isValid()) {
            $file = $request->file('foto_baru');
            $ext  = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $err[] = "Format foto tidak didukung.";
            } elseif ($file->getSize() > 2e6) {
                $err[] = "Foto maksimal 2MB.";
            } else {
                $filename = "u{$id_users}_" . uniqid() . ".$ext";
                $file->move(public_path('image'), $filename);
                $foto = $filename;
            }
        }

        if ($err) {
            session(['flash' => ['ok' => 0, 'msg' => implode(' ', $err)]]);
            return redirect()->route('profile')->with('tab', 'profil');
        }

        $sql = "UPDATE users SET nama=?,nim=?,alamat=?,email=?,no_hp=?" . ($foto ? ",foto_profil=?" : "") . " WHERE id=?";
        $s   = mysqli_prepare($conn, $sql);
        if ($foto) {
            mysqli_stmt_bind_param($s, "ssssssi", $nama, $nim, $alamat, $email, $no_hp, $foto, $id_users);
        } else {
            mysqli_stmt_bind_param($s, "sssssi", $nama, $nim, $alamat, $email, $no_hp, $id_users);
        }

        session(['flash' => mysqli_stmt_execute($s) ? ['ok' => 1, 'msg' => 'Profil diperbarui.'] : ['ok' => 0, 'msg' => 'Gagal.']]);
        return redirect()->route('profile', ['tab' => 'profil']);
    }

    /** POST /profile/password */
    public function updatePassword(Request $request)
    {
        $conn     = DbHelper::connection();
        $id_users = $this->getId();
        if (!$id_users) return redirect()->route('sign-in');

        $lama = $request->input('pwd_lama', '');
        $baru = $request->input('pwd_baru', '');
        $konf = $request->input('pwd_konfirm', '');
        $db   = $this->getProfil($id_users);
        $err  = [];

        if (!$lama || !password_verify($lama, $db['password'])) $err[] = "Password lama salah.";
        elseif (strlen($baru) < 8) $err[] = "Minimal 8 karakter.";
        elseif ($baru === $lama) $err[] = "Sama dengan yang lama.";
        elseif ($baru !== $konf) $err[] = "Konfirmasi salah.";

        if ($err) {
            session(['flash' => ['ok' => 0, 'msg' => implode(' ', $err)]]);
            return redirect()->route('profile', ['tab' => 'keamanan']);
        }

        $hash = password_hash($baru, PASSWORD_BCRYPT);
        $s    = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
        mysqli_stmt_bind_param($s, "si", $hash, $id_users);
        session(['flash' => mysqli_stmt_execute($s) ? ['ok' => 1, 'msg' => 'Password berhasil diperbarui.'] : ['ok' => 0, 'msg' => 'Gagal.']]);

        return redirect()->route('profile', ['tab' => 'keamanan']);
    }
}
