<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sewa Loker - KuLocker</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
</head>
<body>

<div class="container">
    
    <div class="header-section">
        <a href="{{ route('dashboard') }}" class="btn-back">
            <i class="ti ti-arrow-left"></i>
        </a>
        <h1 class="page-title">Riwayat Penggunaan</h1>
    </div>

    <div class="history-grid">
        @if (!empty($riwayat_list))
            @foreach ($riwayat_list as $row)
                @php
                    $status_sewa = strtolower($row['status']);
                    $tgl_pinjam = date('d M Y', strtotime($row['tanggal_mulai']));
                @endphp
                <div class="history-card">
                    <div class="card-left">
                        <div class="locker-icon-box">
                            <i class="ti ti-lock-square-rounded"></i>
                        </div>
                        <div class="locker-info-meta">
                            <div class="locker-code">{{ $row['kode_loker'] }} <span style="font-weight: normal; font-size: 12px; color:#94a3b8;">({{ $row['ukuran'] }})</span></div>
                            <div class="locker-location">
                                <i class="ti ti-building" style="font-size: 14px;"></i> 
                                {{ $row['lokasi'] }}
                            </div>
                            <div class="locker-date">
                                <i class="ti ti-calendar-event"></i> Digunakan pada: {{ $tgl_pinjam }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-right">
                        <span class="status-badge badge-{{ $status_sewa }}">
                            {{ $status_sewa }}
                        </span>
                    </div>
                </div>
            @endforeach
        @else 
            <div class="empty-history">
                <i class="ti ti-history-off"></i>
                <p>Kamu belum memiliki riwayat penyewaan loker.</p>
            </div>
        @endif
    </div>

</div>

<script src="{{ asset('js/riwayat.js') }}"></script>
</body>
</html>
