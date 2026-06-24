<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Terkirim</title>
    <link rel="stylesheet" href="{{ asset('css/keluhan.css') }}" />
</head>
<body style="background-color: #fafafa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; font-family: 'DM Sans', sans-serif;">
    <div style="width: 100%; max-width: 600px; margin: 20px; text-align: center; background: #fff; padding: 50px 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 6px solid #c9a84c;">
        <div style="width: 80px; height: 80px; background: #c9a84c; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
            <svg width="40" height="40" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h1 style="color: #111; margin-bottom: 10px;">Berhasil Terkirim!</h1>
        <h3 style="color: #333; margin-bottom: 5px; font-weight: 500;">Terima kasih, {{ $fullName }}</h3>
        <p style="color: #666; line-height: 1.5;">Laporan Anda dengan kategori <strong>{{ $judul_keluhan }}</strong> telah masuk ke database sistem dan akan segera diproses oleh admin.</p>
        <p style="font-size: 13px; color: #888; margin-bottom: 5px;">Tembusan tindak lanjut akan dikirim ke email: <b>{{ $email }}</b></p>
        <p style="font-size: 13px; color: #888; margin-bottom: 30px; font-style: italic;">"{{ $details }}"</p>

        <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('dashboard') }}" style="display: inline-block; padding: 14px 30px; background: #e0e0e0; color: #111; text-decoration: none; border-radius: 30px; font-weight: bold; transition: 0.3s; border: 2px solid #d0d0d0;">Kembali ke dashboard</a>
            <a href="{{ route('keluhan') }}" style="display: inline-block; padding: 14px 30px; background: #c9a84c; color: #fff; text-decoration: none; border-radius: 30px; font-weight: bold; transition: 0.3s;">Kirim Laporan Baru</a>
        </div>
    </div>
</body>
</html>
