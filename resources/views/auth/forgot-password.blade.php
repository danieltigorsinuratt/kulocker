<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lupa Password – KuLocker</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/reset-password.css') }}">
</head>
<body>

<div class="page-wrap">

  <!-- Brand -->
  <div class="brand">
    <div class="brand-name">Ku<span>Locker</span></div>
  </div>

  <!-- Steps -->
  <div class="steps">
    <div class="step {{ $success ? 'done' : 'active' }}">
      <div class="step-dot">{{ $success ? '✓' : '1' }}</div>
      Email
    </div>
    <div class="step-line"></div>
    <div class="step">
      <div class="step-dot">2</div>
      Kode
    </div>
    <div class="step-line"></div>
    <div class="step">
      <div class="step-dot">3</div>
      Password Baru
    </div>
  </div>

  <div class="card">

    @if ($success)

      <!-- STATE: SUKSES KIRIM EMAIL -->
      <div class="icon-ring success">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
      </div>

      <h1>Email terkirim!</h1>
      <p class="subtitle">Kami mengirimkan kode verifikasi ke</p>

      <div class="email-badge">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
        {{ session('reset_email') }}
      </div>

      <p class="subtitle" style="margin-bottom: 24px;">
        Cek inbox atau folder spam kamu. Kode berlaku selama <strong style="color:#fbc531;">10 menit</strong>.
      </p>

      <a href="{{ route('password.verify') }}" class="btn-primary">
        Masukkan Kode Verifikasi
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </a>

    @else

      <!-- STATE: FORM INPUT EMAIL -->
      <div class="icon-ring warning">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
      </div>

      <h1>Lupa password?</h1>
      <p class="subtitle">Masukkan email yang terdaftar. Kami akan mengirimkan kode verifikasi untuk mereset password kamu.</p>

      @if ($error)
        <div class="alert-error">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ $error }}
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="field">
          <label for="email">Alamat Email</label>
          <div class="input-wrap">
            <input
              type="email"
              id="email"
              name="email"
              placeholder="contoh@email.com"
              value="{{ old('email', $old_email ?? '') }}"
              autocomplete="email"
              required
            />
          </div>
          <p class="hint">Gunakan email yang kamu daftarkan di KuLocker.</p>
        </div>

        <hr class="divider" />

        <button type="submit" class="btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"/>
            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
          </svg>
          Kirim Kode Verifikasi
        </button>

      </form>

    @endif

    <a href="{{ route('sign-in') }}" class="back-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      Kembali ke Sign In
    </a>

  </div>
</div>

</body>
</html>
