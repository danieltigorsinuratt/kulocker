<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KuLocker - Sign Up</title>
    <link rel="stylesheet" href="{{ asset('css/SignUp.css') }}" />
  </head>
  <body>
    <div class="background-container">
      <a href="{{ route('landing') }}" class="back-btn" aria-label="Back to home">
          <img src="{{ asset('img/panah-kiri-svg.svg') }}" alt="Kembali" width="24" height="24">  
      </a>
      <div class="login-card">
        <div class="card-left">
          <div class="overlay-text">
            <p>Welcome to KuLocker</p>
          </div>
        </div>

        <div class="card-right">
          <img src="{{ asset('img/Kulocker-removebg-preview.png') }}" alt="Logo">
          <h2 class="form-title"><i>Create</i> Your Account</h2>

          @if(session('error') || !empty($error))
            <div class="login-error show" style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px;">
              {{ session('error') ?: $error }}
            </div>
          @endif

          <form action="{{ route('register.post') }}" method="POST" id="signupForm" class="form-sign-up">
            @csrf
            <div class="input-group">
              <label for="nama">Nama Lengkap :</label>
              <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda" required value="{{ old('nama') }}" />
            </div>

            <div class="input-group">
              <label for="email">Email :</label>
              <input type="email" id="email" name="email" placeholder="contoh@email.com" required value="{{ old('email') }}" />
            </div>

            <div class="input-group">
              <label for="nim">NIM :</label>
              <input type="text" id="nim" name="nim" placeholder="Masukkan NIM (contoh: F1D0241...)" required value="{{ old('nim') }}" />
            </div>

            <div class="input-group">
              <label for="no_hp"> Masukkan Nomor Hp:</label>
              <input type="text" id="no_hp" name="no_hp" placeholder="contoh: 0821.." required value="{{ old('no_hp') }}" />
            </div>

            <div class="input-group">
              <label for="password">Kata Sandi :</label>
              <input type="password" id="password" name="password" placeholder="Buat kata sandi minimal 8 karakter" required />
              
              <span id="togglePassword" style="
                  font-size: 10px;
                  color: #fbc531;
                  cursor: pointer;
                  display: block;
                  margin-top: 5px;
                  font-weight: bold;
                ">
              </span>
            </div>

            <div class="input-group">
              <label for="confirm_password">Konfirmasi Kata Sandi :</label>
              <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi kata sandi" required />
            </div>

            <hr>

            <div class="sign-in-akun">
                <p>Sudah mempunyai akun?<a href="{{ route('sign-in') }}"> <u>Masuk disini</u></a></p>
            </div>

            <button type="submit" name="submit" class="btn-submit">Daftar</button>
        </form>
        </div>
      </div>
    </div>
    <script src="{{ asset('js/sign-up.js') }}"></script>
  </body>
</html>
