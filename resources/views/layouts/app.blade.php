<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IT Support') — HelpDesk</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="{{ auth()->check() && auth()->user()->isAgent() ? 'is-admin' : '' }}">

<div class="app-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fa-solid fa-headset"></i>
            </div>
            <div class="brand-text">
                <span class="brand-name">HelpDesk</span>
                <span class="brand-tag">UVD</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->user()->isAgent())
            <div class="nav-section">
                <span class="nav-section-label">Dashboard</span>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie nav-icon"></i>
                    <span>Overview</span>
                </a>
            </div>
            <div class="nav-section">
                <span class="nav-section-label">Manajemen</span>
                <a href="{{ route('admin.tickets') }}" class="nav-item {{ request()->routeIs('admin.tickets*') ? 'active' : '' }}">
                    <i class="fa-solid fa-ticket nav-icon"></i>
                    <span>Semua Tiket</span>
                    @php $openCount = \App\Models\Ticket::where('status','open')->count() @endphp
                    @if($openCount > 0)
                    <span class="nav-badge">{{ $openCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users nav-icon"></i>
                    <span>Pengguna</span>
                </a>
                <a href="{{ route('admin.categories') }}" class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags nav-icon"></i>
                    <span>Kategori</span>
                </a>
            </div>
            @else
            <div class="nav-section">
                <span class="nav-section-label">Tiket Saya</span>
                <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list nav-icon"></i>
                    <span>Semua Tiket</span>
                </a>
                <a href="{{ route('tickets.create') }}" class="nav-item {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus nav-icon"></i>
                    <span>Buat Tiket Baru</span>
                </a>
            </div>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="user-avatar">
                <div class="user-info">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn" title="Keluar">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-actions">
                @if(!auth()->user()->isAgent())
                <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus"></i> Tiket Baru
                </a>
                @endif
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
                <button class="alert-close" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
                <button class="alert-close" onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<!-- Overlay for mobile sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('visible');
    });
    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('visible');
    });
</script>
@stack('scripts')
</body>
</html>
