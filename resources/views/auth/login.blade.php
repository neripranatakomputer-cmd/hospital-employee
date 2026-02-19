<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RSUD Kamang Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 420px;
            max-width: 95vw;
        }
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            padding: 40px 30px 30px;
            text-align: center;
        }
        .login-header .icon {
            width: 70px; height: 70px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
        }
        .login-body { padding: 30px; }
        .form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,0.15); }
        .btn-login { background: linear-gradient(135deg, #0d6efd, #0043a8); border: none; height: 48px; font-size: 15px; font-weight: 600; }
        .btn-login:hover { opacity: 0.9; }
        .input-group-text { background: #f8f9fa; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
    <img src="{{ asset('images/logo.png') }}" alt="Logo RSUD Kamang Baru"
        style="width:100px;height:100px;object-fit:contain;filter:brightness(0) invert(1);margin-bottom:12px">
    <h4 class="text-white mb-1 fw-bold">RSUD KAMANG BARU</h4>
    <p class="text-white-50 mb-0 small">Sistem Informasi Manajemen Karyawan</p>
    </div>

    <div class="login-body">
        <h6 class="text-muted mb-4 text-center">Masuk ke Akun Anda</h6>

        @if($errors->any())
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="email@rumahsakit.com" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password"
                        class="form-control" placeholder="Masukkan password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePwd">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label small" for="remember">Ingat Saya</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100 rounded-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            &copy; {{ date('Y') }} SIMKA - Sistem Manajemen Karyawan
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePwd').addEventListener('click', function() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
</script>
</body>
</html>
