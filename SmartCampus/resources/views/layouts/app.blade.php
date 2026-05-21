<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SmartCampus') }} — @yield('title', 'Dashboard')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sc-primary: #4F46E5;
            --sc-primary-dark: #3730A3;
            --sc-secondary: #7C3AED;
            --sc-success: #059669;
            --sc-warning: #D97706;
            --sc-danger: #DC2626;
            --sc-sidebar-width: 260px;
            --sc-sidebar-bg: #1E1B4B;
            --sc-sidebar-text: #C7D2FE;
            --sc-sidebar-active: #4F46E5;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: #F1F5F9;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sc-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sc-sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--sc-sidebar-bg) 0%, #312E81 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sc-sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sc-sidebar-brand h4 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.25rem;
        }

        .sc-sidebar-brand small {
            color: var(--sc-sidebar-text);
            font-size: 0.75rem;
        }

        .sc-nav-section {
            padding: 1rem 0.75rem;
        }

        .sc-nav-section-title {
            color: rgba(255,255,255,0.4);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 0.75rem;
            margin-bottom: 0.5rem;
        }

        .sc-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            border-radius: 0.5rem;
            color: var(--sc-sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .sc-nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .sc-nav-link.active {
            background: var(--sc-sidebar-active);
            color: #fff;
            font-weight: 500;
        }

        .sc-nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        /* ── Main Content ── */
        .sc-main {
            margin-left: var(--sc-sidebar-width);
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .sc-topbar {
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .sc-topbar-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1E293B;
        }

        .sc-user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sc-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--sc-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .sc-role-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            font-weight: 500;
        }

        /* ── Content Area ── */
        .sc-content {
            padding: 1.5rem;
        }

        /* ── Cards ── */
        .sc-stat-card {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 1px solid #E2E8F0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .sc-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .sc-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .sc-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1E293B;
        }

        .sc-stat-label {
            font-size: 0.8rem;
            color: #64748B;
            font-weight: 500;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sc-sidebar {
                transform: translateX(-100%);
            }
            .sc-sidebar.show {
                transform: translateX(0);
            }
            .sc-main {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sc-sidebar" id="sidebar">
        <div class="sc-sidebar-brand">
            <h4><i class="bi bi-mortarboard-fill"></i> SmartCampus</h4>
            <small>Sistem Manajemen Tugas</small>
        </div>

        <nav class="sc-nav-section">
            <div class="sc-nav-section-title">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="sc-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            {{-- Mahasiswa Menu --}}
            @if(Auth::user()->role === 'mahasiswa')
                <div class="sc-nav-section-title mt-3">Akademik</div>
                <a href="{{ route('mahasiswa.assignments.index') }}" class="sc-nav-link {{ request()->routeIs('mahasiswa.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Daftar Tugas
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-book"></i> Mata Kuliah
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-bell"></i> Notifikasi
                </a>
            @endif

            {{-- Dosen Menu --}}
            @if(Auth::user()->role === 'dosen')
                <div class="sc-nav-section-title mt-3">Manajemen</div>
                <a href="{{ route('dosen.assignments.index') }}" class="sc-nav-link {{ request()->routeIs('dosen.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-plus"></i> Kelola Tugas
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-check2-square"></i> Penilaian
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-people"></i> Monitor Mahasiswa
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-file-earmark-arrow-down"></i> Export Laporan
                </a>
            @endif

            {{-- Admin Menu --}}
            @if(Auth::user()->role === 'admin')
                <div class="sc-nav-section-title mt-3">Administrasi</div>
                <a href="{{ route('admin.assignments.index') }}" class="sc-nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data"></i> Semua Tugas
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-person-gear"></i> Kelola Pengguna
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-book"></i> Kelola Mata Kuliah
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-person-check"></i> Kelola Enrollment
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-clock-history"></i> Activity Log
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-file-earmark-arrow-down"></i> Export Data
                </a>
            @endif
        </nav>

        {{-- User Info (Bottom) --}}
        <div class="sc-nav-section" style="position: absolute; bottom: 0; width: 100%; border-top: 1px solid rgba(255,255,255,0.1);">
            <div class="d-flex align-items-center gap-2 px-2 mb-2">
                <div class="sc-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div>
                    <div style="color:#fff; font-size:0.85rem; font-weight:500;">{{ Auth::user()->name }}</div>
                    <div style="color:var(--sc-sidebar-text); font-size:0.7rem;">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sc-nav-link w-100 border-0 bg-transparent text-start" style="cursor:pointer;">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="sc-main">
        <!-- Topbar -->
        <header class="sc-topbar">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none me-2" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <span class="sc-topbar-title">@yield('title', 'Dashboard')</span>
            </div>
            <div class="sc-user-info">
                <span class="sc-role-badge bg-primary bg-opacity-10 text-primary">{{ ucfirst(Auth::user()->role) }}</span>
            </div>
        </header>

        <!-- Content -->
        <div class="sc-content">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
