<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SmartCampus') }} — @yield('title', 'Dashboard')</title>
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

            --sc-body-bg: #F1F5F9;
            --sc-card-bg: #FFFFFF;
            --sc-card-border: #E2E8F0;
            --sc-text-main: #1E293B;
            --sc-text-muted: #64748B;
            --sc-input-bg: #FFFFFF;
        }

        html.dark {
            --sc-body-bg: #0F172A;
            --sc-card-bg: #1E293B;
            --sc-card-border: #334155;
            --sc-text-main: #F8FAFC;
            --sc-text-muted: #94A3B8;
            --sc-input-bg: #1E293B;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: var(--sc-body-bg);
            color: var(--sc-text-main);
            min-height: 100vh;
            transition: background 0.2s, color 0.2s;
        }

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

        .sc-main {
            margin-left: var(--sc-sidebar-width);
            min-height: 100vh;
        }

        .sc-topbar {
            background: var(--sc-card-bg);
            border-bottom: 1px solid var(--sc-card-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: background 0.2s, border 0.2s;
        }

        .sc-topbar-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--sc-text-main);
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

        .sc-content {
            padding: 1.5rem;
        }

        .sc-stat-card {
            background: var(--sc-card-bg);
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 1px solid var(--sc-card-border);
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s, border 0.2s;
        }

        .sc-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .sc-stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--sc-text-main);
        }

        .sc-stat-label {
            font-size: 0.8rem;
            color: var(--sc-text-muted);
            font-weight: 500;
        }

        .d-none-theme {
            display: none !important;
        }

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

        html.dark, html.dark body {
            background-color: var(--sc-body-bg) !important;
            color: var(--sc-text-main) !important;
        }

        html.dark .card, 
        html.dark .card-body,
        html.dark .bg-white,
        html.dark .bg-light {
            background-color: var(--sc-card-bg) !important;
            border-color: var(--sc-card-border) !important;
            color: var(--sc-text-main) !important;
        }

        html.dark .card-body *, 
        html.dark .list-group-item *,
        html.dark .profile-section * {
            color: var(--sc-text-main) !important;
        }

        html.dark .text-muted, 
        html.dark .text-secondary, 
        html.dark small,
        html.dark .small {
            color: #94A3B8 !important;
        }

        html.dark .list-group-item {
            background-color: var(--sc-card-bg) !important;
            border-color: var(--sc-card-border) !important;
            color: var(--sc-text-main) !important;
        }

        html.dark .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        html.dark .form-control, 
        html.dark .form-select, 
        html.dark .form-control[readonly] {
            background-color: var(--sc-input-bg) !important;
            border-color: var(--sc-card-border) !important;
            color: var(--sc-text-main) !important;
        }

        html.dark .form-control:focus, html.dark .form-select:focus {
            border-color: var(--sc-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
        }

        html.dark .table {
            color: var(--sc-text-main) !important;
        }

        html.dark .table th, html.dark .table td {
            border-color: var(--sc-card-border) !important;
            background-color: var(--sc-card-bg) !important;
        }

        html.dark .table td,
        html.dark .table td *,
        html.dark .table .text-muted,
        html.dark .table small {
            color: #F8FAFC !important; 
        }

        html.dark .table td:nth-child(4), 
        html.dark .table td:nth-child(4) *,
        html.dark .table td:has(i.bi-laptop),
        html.dark .table td:has(i.bi-laptop) * {
            color: #F87171 !important; 
            font-weight: 600 !important;
        }

        html.dark .table td .bi-clock,
        html.dark .table .text-muted i {
            color: #94A3B8 !important;
        }
        
        html.dark .table td .bi-laptop {
            color: #F87171 !important;
        }

        html.dark .sc-sidebar span,
        html.dark .sc-sidebar .ms-3,
        html.dark .sc-sidebar .ms-3 *,
        html.dark .sc-sidebar .fw-bold {
            color: #FFFFFF !important;
        }

        html.dark .sc-sidebar .ms-3 span:last-child,
        html.dark .sc-sidebar small,
        html.dark .sc-sidebar .text-muted {
            color: #94A3B8 !important;
        }

        html.dark .alert-success, 
        html.dark .bg-success-subtle,
        html.dark [class*="-subtle"] {
            background-color: rgba(16, 185, 129, 0.15) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        html.dark .alert-success *, 
        html.dark .bg-success-subtle * {
            color: #10B981 !important;
        }
    </style>

    @stack('styles')
</head>
<body>

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

            {{-- Menu untuk Mahasiswa --}}
            @if(Auth::user()->role === 'mahasiswa')
                <div class="sc-nav-section-title mt-3">Akademik</div>
                <a href="{{ route('mahasiswa.assignments.index') }}" class="sc-nav-link {{ request()->routeIs('mahasiswa.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Daftar Tugas
                </a>
                <a href="{{ route('mahasiswa.courses.index') }}" class="sc-nav-link {{ request()->routeIs('mahasiswa.courses.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Mata Kuliah
                </a>
                <a href={{--"{{ route('enrollments.index') }}"--}} class="sc-nav-link {{ request()->routeIs('enrollments.*') ? 'active' : '' }}">
                    <i class="bi bi-compass"></i> Eksplor Kelas
                </a>
                <a href="{{ route('notifications.index') }}" class="sc-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i> Notifikasi
                </a>
            @endif

            {{-- Menu untuk Dosen --}}
            @if(Auth::user()->role === 'dosen')
                <div class="sc-nav-section-title mt-3">Manajemen</div>
                <a href="{{ route('dosen.assignments.index') }}" class="sc-nav-link {{ request()->routeIs('dosen.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-plus"></i> Kelola Tugas
                </a>
                <a href="{{ route('dosen.courses.index') }}" class="sc-nav-link {{ request()->routeIs('dosen.courses.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Monitor Kelas
                </a>
                <a href="{{ route('notifications.index') }}" class="sc-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i> Notifikasi
                </a>
            @endif

            {{-- Menu untuk Administrator --}}
            @if(Auth::user()->role === 'admin')
                <div class="sc-nav-section-title mt-3">Administrasi</div>
                <a href="{{ route('admin.users.index') }}" class="sc-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Kelola Pengguna
                </a>
                <a href="{{ route('admin.courses.index') }}" class="sc-nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Mata Kuliah
                </a>
                <a href="{{ route('admin.enrollments.index') }}" class="sc-nav-link {{ request()->routeIs('admin.enrollments.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check"></i> Kelola Enrollment
                </a>
                <a href="{{ route('admin.announcements.index') }}" class="sc-nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i> Pengumuman
                </a>

                <div class="sc-nav-section-title mt-3">Riwayat</div>
                <a href="{{ route('admin.activity-logs.index') }}" class="sc-nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Activity Log
                </a>

            @endif
        </nav>

        {{-- Panel Informasi User (Bagian Bawah Sidebar) --}}
        <div class="sc-nav-section" style="position: absolute; bottom: 0; width: 100%; border-top: 1px solid rgba(255,255,255,0.1);">
            <div class="d-flex align-items-center gap-2 px-2 mb-2">
                <div class="sc-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="ms-3">
                    <div class="fw-bold" style="color:#fff; font-size:0.85rem;">{{ Auth::user()->name }}</div>
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

    <main class="sc-main">
        <header class="sc-topbar">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none me-2" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <span class="sc-topbar-title">@yield('title', 'Dashboard')</span>
            </div>
            <div class="sc-user-info">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" type="button" class="btn btn-link text-secondary p-2 me-2" style="text-decoration: none; box-shadow: none;">
                    <i id="theme-icon" class="bi bi-sun-fill" style="font-size: 1.2rem; color: #EAB308;"></i>
                </button>


                <span class="sc-role-badge bg-primary bg-opacity-10 text-primary">{{ ucfirst(Auth::user()->role) }}</span>
            </div>
        </header>

        <div class="sc-content">
            {{-- Flash Session Notifications --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                    <div>
                        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    </div>
                    @if(Auth::check() && Auth::user()->role === 'dosen' && session('show_undo'))
                        <form action="{{ route('dosen.assignments.undo') }}" method="POST" class="d-inline me-4">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success py-0 border-0 text-decoration-underline" style="font-size: 0.85rem;">
                                <i class="bi bi-arrow-counterclockwise"></i> Batal (Undo)
                            </button>
                        </form>
                    @endif
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const htmlElement = document.documentElement;
    
            function updateThemeUI() {
                if (htmlElement.classList.contains('dark')) {
                    themeIcon.className = 'bi bi-moon-stars-fill';
                    themeIcon.style.color = '#94A3B8';
                    // Trigger Dark Mode untuk Bootstrap 5
                    htmlElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    themeIcon.className = 'bi bi-sun-fill';
                    themeIcon.style.color = '#EAB308';
                    // Trigger Light Mode untuk Bootstrap 5
                    htmlElement.setAttribute('data-bs-theme', 'light');
                }
            }
    
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }
            
            updateThemeUI();
    
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    if (htmlElement.classList.contains('dark')) {
                        htmlElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        htmlElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    updateThemeUI();
                });
            }
        });
    </script>
</body>
</html>