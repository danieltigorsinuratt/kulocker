<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KuLocker - Sign In</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/sign-in.css') }}" />
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
          <h2 class="form-title"><i>Sign-In</i> to your account</h2>

          @if(session('error') || !empty($error))
            <div class="login-error show">
              {{ session('error') ?: $error }}
            </div>
          @endif
          
          <!--FORM-->
          <form class="form-card-sign" action="{{ route('login.post') }}" method="POST" id="signinForm" novalidate>
            @csrf
            <div class="input-group">
              <label for="nim">NIM :</label>
              <input type="text" id="nim" name="nim" required value="{{ old('nim') }}" />
            </div>

            <div class="input-group">
              <label for="password">Password :</label>
              <input class="field__input" type="password" id="password" name="password" required />

              <span
                id="togglePassword"
                style="
                  font-size: 10px;
                  color: #fbc531;
                  cursor: pointer;
                  display: block;
                  margin-top: 5px;
                  font-weight: bold;
                "
              ></span>
            </div>

            <hr>

            <div class="forgot-password">
                <a href="{{ route('password.request') }}"> <u>Lupa password?</u></a>
                <a href="{{ route('register') }}"> <u>Buat Akun Baru</u></a>
            </div>

            <button type="submit" class="btn-submit" name="signin" id="submitBtn">Sign In</button>
          </form>
        </div>
      </div>
    </div>
    <script src="{{ asset('js/sign-in.js') }}"></script>
  </body>
</html>
