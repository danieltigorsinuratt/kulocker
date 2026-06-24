<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tambah Unit Locker - KuLocker</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div style="max-width:900px;margin:40px auto;padding:20px;">
        <a href="{{ route('admin', ['page' => 'locker']) }}" style="text-decoration:none;color:#0f766e;font-weight:700;">← Kembali ke Daftar Locker</a>
        <h2 style="margin-top:18px;margin-bottom:8px;">Tambah Unit Locker</h2>

        @if ($msg === 'exists')
            <div style="background:#fff7ed;color:#92400e;padding:12px;border-radius:8px;margin-bottom:12px;">Kode locker sudah ada.</div>
        @elseif ($msg === 'invalid')
            <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;margin-bottom:12px;">Mohon isi semua kolom dengan benar.</div>
        @endif

        <form method="POST" action="{{ route('admin.tambah-locker.post') }}" style="background:#ffffff;padding:18px;border-radius:10px;border:1px solid #e6e6e6;">
            @csrf
            <div style="display:grid;gap:12px;">
                <label style="font-weight:600;">Kode Locker</label>
                <input type="text" name="kode_loker" placeholder="Contoh: LKR-001" required style="padding:10px;border-radius:8px;border:1px solid #d1d5db;" value="{{ old('kode_loker') }}">

                <label style="font-weight:600;">Lokasi Cluster</label>
                <input type="text" name="lokasi" placeholder="Contoh: Cluster A" required style="padding:10px;border-radius:8px;border:1px solid #d1d5db;" value="{{ old('lokasi') }}">

                <label style="font-weight:600;">Ukuran</label>
                <select name="ukuran" required style="padding:10px;border-radius:8px;border:1px solid #d1d5db;">
                    <option value="">Pilih ukuran</option>
                    <option value="S" {{ old('ukuran') === 'S' ? 'selected' : '' }}>S</option>
                    <option value="M" {{ old('ukuran') === 'M' ? 'selected' : '' }}>M</option>
                    <option value="L" {{ old('ukuran') === 'L' ? 'selected' : '' }}>L</option>
                </select>

                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:6px;">
                    <a href="{{ route('admin', ['page' => 'locker']) }}" style="background:#e5e7eb;padding:10px 16px;border-radius:8px;color:#111827;text-decoration:none;font-weight:600;">Batal</a>
                    <button type="submit" style="background:#0f766e;color:white;padding:10px 16px;border-radius:8px;border:none;font-weight:700;">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
