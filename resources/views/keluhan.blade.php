<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Pengaduan Pelanggan</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/keluhan.css') }}" />
</head>
<body>

    <a href="{{ route('dashboard') }}" class="back-btn" title="Kembali">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
    </a>

    <div class="background-container">
        <div class="main-container">
            
            <div class="card-left">
                <div>
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                    <h1>Sampaikan<br>Suara Anda</h1>
                    <p>Kami sangat menghargai setiap masukan dan keluhan Anda. Beritahu kami agar kami bisa memberikan layanan yang lebih baik ke depannya.</p>
                </div>
                <div class="overlay-text">
                    Layanan Pelanggan 24/7
                </div>
            </div>

            <div class="card-right">
                <h2 class="form-title">Form Keluhan</h2>
                
                @if (session('error'))
                    <div style="background-color: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-weight: 500; font-size: 14px;">
                        ⚠️ {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('keluhan.post') }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-0 sm:gap-4">
                        <div class="input-group w-full">
                            <label for="fullName">Nama Lengkap</label>
                            <input type="text" id="fullName" name="fullName" required value="{{ old('fullName', $user_data['nama']) }}" placeholder="Budi Santoso">
                        </div>
                        <div class="input-group w-full">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" name="email" required value="{{ old('email', $user_data['email']) }}" placeholder="budi@email.com">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="category">Kategori Keluhan</label>
                        <div class="select-wrapper">
                            <select id="category" name="category" required>
                                <option value="" disabled {{ old('category') === null ? 'selected' : '' }}>Pilih Kategori...</option>
                                <option value="product" {{ old('category') === 'product' ? 'selected' : '' }}>Loker rusak</option>
                                <option value="delivery" {{ old('category') === 'delivery' ? 'selected' : '' }}>Loker tidak bisa dibuka</option>
                                <option value="service" {{ old('category') === 'service' ? 'selected' : '' }}>Pelayanan Tidak Memuaskan</option>
                                <option value="website" {{ old('category') === 'website' ? 'selected' : '' }}>Kendala Website / Aplikasi</option>
                                <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="pemesanan_loker">Loker yang Bermasalah (Opsional)</label>
                        <div class="select-wrapper">
                            <select id="pemesanan_loker" name="pemesanan_loker">
                                <option value="">Tidak Terkait Transaksi Loker / Umum</option>
                                @foreach ($daftar_sewa as $sewa)
                                    <option value="{{ $sewa['pemesanan_id'] . '_' . $sewa['locker_id'] }}" {{ old('pemesanan_loker') === ($sewa['pemesanan_id'] . '_' . $sewa['locker_id']) ? 'selected' : '' }}>
                                        Loker {{ $sewa['kode_loker'] }} - {{ $sewa['lokasi'] }} (Status Sewa: {{ ucfirst($sewa['status']) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="details">Detail Keluhan</label>
                        <textarea id="details" name="details" required placeholder="Jelaskan secara detail kendala yang Anda alami beserta nomor loker atau kendala sistem...">{{ old('details') }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary">
                        Kirim Keluhan
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>
