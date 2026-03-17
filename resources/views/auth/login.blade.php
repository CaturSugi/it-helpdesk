<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HelpDesk Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">
                <i class="fa-solid fa-headset"></i>
            </div>
        </div>
        <h1 class="auth-title">HelpDesk UVD</h1>
        <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>

        @if($errors->any())
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <i class="fa-solid fa-envelope input-group-icon"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="nama@perusahaan.com"
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <i class="fa-solid fa-lock input-group-icon"></i>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-group d-flex justify-between align-center">
                <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;cursor:pointer;">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:.875rem;color:var(--gray-500);">
            Belum punya akun?
            <a href="{{ route('register') }}" style="font-weight:600;">Daftar sekarang</a>
        </p>
    </div>
</div>
</body>
</html>
