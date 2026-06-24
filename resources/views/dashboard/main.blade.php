@php
if (!function_exists('pengumuman_meta')) {
    function pengumuman_meta($kategori) {
        switch ($kategori) {
            case 'promo':       return ['icon' => 'ti-tag',            'color' => 'green',  'label' => 'Promo'];
            case 'peringatan':  return ['icon' => 'ti-alert-triangle', 'color' => 'red',    'label' => 'Peringatan'];
            case 'maintenance': return ['icon' => 'ti-tool',           'color' => 'orange', 'label' => 'Maintenance'];
            default:            return ['icon' => 'ti-info-circle',    'color' => 'blue',   'label' => 'Info'];
        }
    }
}
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utama - KuLocker</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/dashboard-utama.css') }}"/>
</head>
<body>

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav>
    <div class="nav-inner">
        <div class="nav-logo-icon">
            <img src="{{ asset('img/Kulocker.jpeg') }}" alt="Logo Kulocker">
        </div>

        <div class="nav-search-bar" onclick="openMap()">
            <i class="ti ti-search"></i>
            <input type="text" placeholder="Cari Lokasi" readonly />
        </div>

        <div class="user-menu" id="userMenu">
            <div class="user-trigger" onclick="toggleDropdown(event)">
                <span class="user-name">{{ $user['nama'] }}</span>
                <div class="profil">
                    @if (!empty($user['foto_profil']))
                        <img src="{{ asset('image/' . $user['foto_profil']) }}" alt="user">
                    @else
                        <img src="{{ asset('img/profl.png') }}" alt="user">
                    @endif
                </div>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="{{ route('profile') }}"><i class="ti ti-user"></i> Profil Page</a>
                <a href="{{ route('keluhan') }}"><i class="ti ti-message-report"></i> Keluhan & Saran</a>
                <hr class="dropdown-divider">
                <a href="{{ route('logout') }}" class="logout-link"><i class="ti ti-logout"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- ═══════════════ CAROUSEL ═══════════════ -->
<div class="carousel" id="carousel">

    <div class="slide active">
        <div class="slide-bg" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2010 100%);"></div>
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <div class="slide-tag">UNIVERSITAS MATARAM</div>
            <div class="slide-title">
                Welcome back, <em>{{ explode(' ', $user['nama'])[0] }}</em>
            </div>
            <div class="slide-desc">Pesan loker kapan saja, akses dengan kode, dan pantau status penyimpanan kamu secara real-time.</div>
            <button class="slide-btn" onclick="openMap()"><i class="ti ti-lock-square"></i> Pesan Loker Sekarang</button>
        </div>
    </div>

    <div class="slide">
        <div class="slide-bg" style="background: linear-gradient(135deg, #0d1a2d 0%, #1a2d1a 100%);"></div>
        <div class="slide-overlay"></div>
        <div class="slide-content">
            <div class="slide-tag">FITUR BARU</div>
            <div class="slide-title">Titip <em>Barang</em> kamu sekarang</div>
            <div class="slide-desc">Titip Beban Barang Bawaan Kamu dengan Aman dan Nyaman .</div>
            <button class="slide-btn" onclick="openMap()"><i class="ti ti-map-pin"></i> Cari Loker Terdekat</button>
        </div>
    </div>

    <div class="carousel-arrow left" onclick="prevSlide()"><i class="ti ti-chevron-left"></i></div>
    <div class="carousel-arrow right" onclick="nextSlide()"><i class="ti ti-chevron-right"></i></div>
    <div class="carousel-nav" id="carouselNav"></div>
</div>

<!-- ═══════════════ FEATURE BUTTONS ═══════════════ -->
<div class="feature-section">
  <div class="feature-section-inner">
    <div class="feature-label">FITUR UTAMA</div>
    <div class="feature-grid">
        <a href="{{ route('tiket') }}" class="feature-btn">
            <div class="feature-icon gold"><i class="ti ti-layout-grid"></i></div>
            <div class="feature-name">My Locker</div>
            <div class="feature-desc">Status loker aktif & QR Code pemesanan</div>
        </a>
        <div class="feature-btn" onclick="openMap()">
            <div class="feature-icon blue"><i class="ti ti-map-pin"></i></div>
            <div class="feature-name">Pesan Loker</div>
            <div class="feature-desc">Titipkan barang kamu sekarang</div>
        </div>
        <a href="{{ route('locker.riwayat') }}" class="feature-btn">
            <div class="feature-icon green"><i class="ti ti-history"></i></div>
            <div class="feature-name">Riwayat</div>
            <div class="feature-desc">Riwayat penyimpanan loker kamu</div>
        </a>
    </div>
  </div>
</div>

<!-- ═══════════════ PENGUMUMAN ═══════════════ -->
@if (!empty($pengumuman_list))
<section class="pengumuman-section">
    <div class="pengumuman-inner">
        <div class="pengumuman-header">
            <div>
                <div class="feature-label">PENGUMUMAN</div>
                <h2 class="pengumuman-title">Info & Pengumuman</h2>
            </div>
            <a href="{{ route('pengumuman') }}" class="pengumuman-lihat-semua">
                Lihat semua <i class="ti ti-arrow-right"></i>
            </a>
        </div>
        <div class="pengumuman-grid">
            @foreach ($pengumuman_list as $p)
                @php
                    $meta        = pengumuman_meta($p['kategori']);
                    $tanggal     = date('d M Y', strtotime($p['created_at']));
                    $isi_singkat = mb_strlen($p['isi']) > 120 ? mb_substr($p['isi'], 0, 120) . '...' : $p['isi'];
                @endphp
                <div class="pengumuman-card reveal">
                    <div class="pengumuman-card-top">
                        <div class="pengumuman-badge {{ $meta['color'] }}">
                            <i class="ti {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                        </div>
                        <span class="pengumuman-tanggal">{{ $tanggal }}</span>
                    </div>
                    <h3 class="pengumuman-judul">{{ $p['judul'] }}</h3>
                    <p class="pengumuman-isi">{{ $isi_singkat }}</p>
                    @if ($p['expired_at'])
                    <div class="pengumuman-expired">
                        <i class="ti ti-clock"></i>
                        Berlaku hingga {{ date('d M Y', strtotime($p['expired_at'])) }}
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══════════════ MAP MODAL ═══════════════ -->
<div class="modal-overlay" id="mapModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title"><i class="ti ti-map-2"></i> Cari Loker Terdekat</div>
            <button class="modal-close" id="mapClose"><i class="ti ti-x"></i></button>
        </div>
        <div class="modal-search">
            <div class="modal-search-bar">
                <i class="ti ti-search"></i>
                <input type="text" id="mapSearch" placeholder="Cari gedung atau kode loker..."/>
            </div>
        </div>

        @if (!empty($lokasi_list))
        <div class="modal-lokasi-wrap">
            <div class="modal-lokasi-label">Pilih Lokasi</div>
            <div class="modal-lokasi-grid" id="modalLokasiGrid">
                @foreach ($lokasi_list as $lok)
                    @php
                        $slug         = urlencode($lok['lokasi']);
                        $status_class = $lok['tersedia'] > 0 ? 'ada' : 'penuh';
                        $status_label = $lok['tersedia'] > 0 ? $lok['tersedia'] . ' tersedia' : 'Penuh';
                    @endphp
                    <a href="{{ route('locker.selection') }}?lokasi={{ $slug }}"
                       class="modal-lokasi-card"
                       data-lokasi="{{ strtolower(htmlspecialchars($lok['lokasi'])) }}">
                        <div class="modal-lokasi-icon">
                            <i class="ti ti-building"></i>
                        </div>
                        <div class="modal-lokasi-info">
                            <span class="modal-lokasi-name">{{ $lok['lokasi'] }}</span>
                            <span class="modal-lokasi-sub">{{ $lok['total'] }} loker</span>
                        </div>
                        <span class="modal-lokasi-badge {{ $status_class }}">{{ $status_label }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
        <div id="map"></div>
        <div class="modal-list" id="lokerList"></div>
    </div>
</div>
<!-- ═══════════════ FOOTER ═══════════════ -->
<footer>
    <div class="footer-inner">
        <div class="footer-bottom">
            <p class="footer-copy">© 2026 Kulocker. All rights reserved.</p>
        </div>
    </div>
</footer>

<iframe src="{{ route('cron.pengingat') }}" style="display:none; width:0; height:0; border:none;"></iframe>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="{{ asset('js/dashboard-utama.js') }}"></script>
</body>
</html>
