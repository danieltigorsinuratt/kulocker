<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menyiapkan Loker Anda...</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        
        .loading-container { width: 100%; max-width: 400px; background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 40px 30px; text-align: center; border: 1px solid #e5e7eb; }
        
        .spinner { width: 55px; height: 55px; border: 5px solid #f3f3f3; border-top: 5px solid #0f172a; border-radius: 50%; margin: 0 auto 24px; animation: spin 1s linear infinite; }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .title { font-size: 1.3rem; font-weight: 700; color: #111827; margin-bottom: 10px; }
        .subtitle { font-size: 0.9rem; color: #6b7280; line-height: 1.5; }
    </style>
</head>
<body>

    <div class="loading-container">
        <div class="spinner"></div>
        
        <h1 class="title">Menyiapkan Akses Loker</h1>
        <p class="subtitle">Sistem sedang memproses hak akses IoT Anda. Mohon tunggu sejenak...</p>
    </div>

    <form id="redirectForm" action="{{ route('locker.sukses') }}" method="POST">
        @csrf
        <input type="hidden" name="pemesanan_id" value="{{ $pemesanan_id }}">
    </form>

    <script>
        const form = document.getElementById('redirectForm');
        setTimeout(() => {
            form.submit(); 
        }, 2000);
    </script>
</body>
</html>
