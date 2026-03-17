<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — HelpDesk UVD</title>
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
        <h1 class="auth-title">Buat Akun Baru</h1>
        <p class="auth-subtitle">Daftarkan diri untuk mengajukan tiket IT Support</p>

        @if($errors->any())
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fa-solid fa-user input-group-icon"></i>
                    <input type="text" name="name" class="form-control"
                           placeholder="Nama lengkap Anda" value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fa-solid fa-envelope input-group-icon"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="nama@perusahaan.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Departemen</label>
                    <div class="input-group">
                        <i class="fa-solid fa-building input-group-icon"></i>
                        <input type="text" name="department" class="form-control"
                               placeholder="Contoh: Finance" value="{{ old('department') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <div class="input-group">
                        <i class="fa-solid fa-phone input-group-icon"></i>
                        <input type="text" name="phone" class="form-control"
                               placeholder="08xx-xxxx-xxxx" value="{{ old('phone') }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fa-solid fa-lock input-group-icon"></i>
                    <input type="password" name="password" class="form-control"
                           placeholder="Minimal 8 karakter" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                <div class="input-group">
                    <i class="fa-solid fa-lock input-group-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:.875rem;color:var(--gray-500);">
            Sudah punya akun?
            <a href="{{ route('login') }}" style="font-weight:600;">Masuk di sini</a>
        </p>
    </div>
</div>
</body>
</html>
