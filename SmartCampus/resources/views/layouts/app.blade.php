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
                <a href="{{ route('notifications.index') }}" class="sc-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i> Notifikasi
                </a>
                <div class="sc-nav-section-title mt-3">Riwayat</div>
                <a href="{{ route('mahasiswa.activity-logs.index') }}" class="sc-nav-link {{ request()->routeIs('mahasiswa.activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Aktivitas
                </a>

            @endif

            {{-- Menu untuk Dosen --}}
            @if(Auth::user()->role === 'dosen')
                <div class="sc-nav-section-title mt-3">Manajemen</div>
                <a href="{{ route('dosen.assignments.index') }}" class="sc-nav-link {{ request()->routeIs('dosen.assignments.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-plus"></i> Kelola Tugas
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-people"></i> Monitor Mahasiswa
                </a>
                <a href="#" class="sc-nav-link">
                    <i class="bi bi-file-earmark-arrow-down"></i> Export Laporan
                </a>
                <div class="sc-nav-section-title mt-3">Riwayat</div>
                <a href="{{ route('dosen.activity-logs.index') }}" class="sc-nav-link {{ request()->routeIs('dosen.activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat Aktivitas
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

                <!-- Dropdown Lonceng Notifikasi -->
                <div class="dropdown me-2" id="loncengDropdown">
                    <button class="btn btn-link text-secondary position-relative p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="loncengBtn">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge" style="font-size: 0.6rem; padding: 0.25em 0.5em;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-0" style="width: 320px; z-index: 1100;" aria-labelledby="loncengBtn">
                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-semibold text-dark small" style="font-size: 0.85rem;">Notifikasi Baru</span>
                            <button id="markAllReadLonceng" class="btn btn-link text-primary p-0 small text-decoration-none d-none" style="font-size: 0.75rem;">Tandai semua terbaca</button>
                        </div>
                        <div class="list-group list-group-flush overflow-auto" style="max-height: 250px;" id="loncengNotifList">
                            <!-- Loader -->
                            <div class="text-center py-3 text-muted" id="loncengLoader">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ms-1 small" style="font-size: 0.8rem;">Memuat...</span>
                            </div>
                        </div>
                        <div class="p-2 border-top text-center bg-light rounded-bottom-3">
                            <a href="{{ route('notifications.index') }}" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.8rem;">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>
                <span class="sc-role-badge bg-primary bg-opacity-10 text-primary">{{ ucfirst(Auth::user()->role) }}</span>
            </div>
        </header>

        <div class="sc-content">
            {{-- Flash Session Notifications --}}
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Notification Lonceng AJAX Global Script -->
    <script>
    function refreshLonceng() {
        const badge = document.getElementById('notifBadge');
        const listContainer = document.getElementById('loncengNotifList');
        const markAllBtn = document.getElementById('markAllReadLonceng');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("notifications.unread") }}')
            .then(response => response.json())
            .then(data => {
                // Update badge count
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.classList.remove('d-none');
                    markAllBtn.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                    badge.textContent = '0';
                    markAllBtn.classList.add('d-none');
                }

                // Update list
                listContainer.innerHTML = '';
                if (data.notifications.length === 0) {
                    listContainer.innerHTML = `
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-bell-slash fs-5 d-block mb-1"></i>
                            Tidak ada notifikasi baru.
                        </div>
                    `;
                } else {
                    data.notifications.forEach(notif => {
                        const icon = notif.channel === 'email' ? 'bi-envelope-fill text-info bg-info bg-opacity-10' : 'bi-layout-text-window-reverse text-primary bg-primary bg-opacity-10';
                        const title = (notif.assignment && notif.assignment.course) 
                            ? `${notif.assignment.course.name} - ${notif.assignment.title}` 
                            : (notif.assignment ? notif.assignment.title : 'Pemberitahuan');
                        const notifItem = document.createElement('div');
                        notifItem.className = 'list-group-item list-group-item-action p-3 border-0 border-bottom d-flex align-items-start gap-2 position-relative';
                        notifItem.style.cursor = 'pointer';
                        
                        notifItem.innerHTML = `
                            <span class="avatar-icon rounded-circle p-1.5 d-flex align-items-center justify-content-center ${icon}" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                <i class="bi ${notif.channel === 'email' ? 'bi-envelope' : 'bi-layout-text-window-reverse'}"></i>
                            </span>
                            <div class="flex-grow-1 pe-4" onclick="window.location.href='{{ route("notifications.index") }}'">
                                <div class="fw-semibold text-dark text-truncate" style="font-size: 0.8rem; max-width: 200px;">${title}</div>
                                <div class="text-secondary text-wrap" style="font-size: 0.75rem; line-height: 1.3;">${notif.message}</div>
                            </div>
                            <button class="btn btn-link text-primary p-0 position-absolute end-0 top-0 mt-3 me-2 lonceng-mark-read" data-id="${notif.id}" title="Tandai Terbaca" style="z-index: 10;">
                                <i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i>
                            </button>
                        `;
                        listContainer.appendChild(notifItem);
                    });

                    // Event handler tandai terbaca per notifikasi di lonceng
                    document.querySelectorAll('.lonceng-mark-read').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const id = this.getAttribute('data-id');
                            fetch(`/notifications/${id}/read`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                            .then(response => response.json())
                            .then(res => {
                                if (res.success) {
                                    refreshLonceng();
                                    // Jika kita sedang berada di halaman index notifikasi, refresh juga halamannya
                                    if (window.location.pathname.startsWith('/notifications')) {
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    });
                }
            })
            .catch(err => {
                console.error('Error fetching notifications:', err);
                listContainer.innerHTML = '<div class="text-center py-3 text-danger small">Gagal memuat notifikasi.</div>';
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Fetch notifikasi saat halaman load
        refreshLonceng();

        // Tombol tandai semua terbaca di lonceng dropdown
        const markAllBtn = document.getElementById('markAllReadLonceng');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    refreshLonceng();
                    if (window.location.pathname.startsWith('/notifications')) {
                        window.location.reload();
                    }
                }
            });
        });

        // Set interval untuk polling notifikasi berkala (misal tiap 60 detik)
        setInterval(refreshLonceng, 60000);
    });
    </script>

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
                } else {
                    themeIcon.className = 'bi bi-sun-fill';
                    themeIcon.style.color = '#EAB308';
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