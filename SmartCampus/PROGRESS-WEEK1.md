# PROGRESS WEEK 1 — SmartCampus
**Mata Kuliah:** Pola Desain Perangkat Lunak  
**Anggota:** Francisco Valentino (2472040)  
**Branch:** `Week-1---Francisco-Valentino`  
**Tanggal:** 8 Mei 2026  

---

## 1. Inisialisasi Project Laravel

Membuat project Laravel 12 sebagai fondasi sistem SmartCampus.

- Framework: **Laravel 12.58.0**
- PHP: **8.2.12**
- Package Manager: **Composer 2.9.5**
- Project dibuat menggunakan `composer create-project laravel/laravel`
- Project disimpan di dalam folder `SmartCampus/`

---

## 2. Konfigurasi Database MySQL

Mengubah konfigurasi `.env` dari SQLite (default) ke **MySQL** agar bisa menggunakan MySQL Workbench.

**File:** `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartcampus
DB_USERNAME=root
DB_PASSWORD=
```

Database `smartcampus` dibuat manual di MySQL Workbench:
```sql
CREATE DATABASE IF NOT EXISTS smartcampus
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 3. Database Migrations (12 Tabel)

Membuat 12 migration file yang merepresentasikan seluruh entitas di ERD SmartCampus.

| No | Tabel | Deskripsi |
|----|-------|-----------|
| 1 | `users` | Modifikasi — ditambah kolom `role`, `otp_code`, `otp_expiry`, `is_active` |
| 2 | `students` | Profil mahasiswa (nim, program_studi, semester, angkatan) |
| 3 | `lecturers` | Profil dosen (nip, department, jabatan) |
| 4 | `courses` | Mata kuliah (code, sks, kuota, is_active) |
| 5 | `enrollments` | Junction table Student-Course (status: pending/active/dropped) |
| 6 | `assignments` | Tugas dosen (deadline, max_score, file_format_allowed) |
| 7 | `submissions` | Pengumpulan tugas (progress enum untuk State Pattern) |
| 8 | `grades` | Penilaian (grading_strategy untuk Strategy Pattern) |
| 9 | `notifications` | Notifikasi multi-channel (Observer Pattern) |
| 10 | `activity_logs` | Audit trail (Singleton Pattern, detail_json untuk Undo/Redo) |
| 11 | `export_logs` | Riwayat ekspor PDF/CSV |
| 12 | `ui_preferences` | Preferensi tema light/dark per user |

**Lokasi file:** `database/migrations/`

---

## 4. Eloquent Models (12 Model)

Membuat 12 model Eloquent lengkap dengan relasi antar entitas sesuai ERD.

| Model | Relasi Utama |
|-------|-------------|
| `User` | hasOne Student/Lecturer/UiPreference, hasMany Notification/ActivityLog/ExportLog |
| `Student` | belongsTo User, hasMany Enrollment/Submission |
| `Lecturer` | belongsTo User, hasMany Course |
| `Course` | belongsTo Lecturer, hasMany Enrollment/Assignment |
| `Enrollment` | belongsTo Student/Course/User(verifier) |
| `Assignment` | belongsTo Course/User(creator), hasMany Submission/Notification |
| `Submission` | belongsTo Assignment/Student, hasMany Grade |
| `Grade` | belongsTo Submission/User(grader) |
| `Notification` | belongsTo User/Assignment |
| `ActivityLog` | belongsTo User |
| `ExportLog` | belongsTo User(requester) |
| `UiPreference` | belongsTo User |

**Lokasi file:** `app/Models/`

---

## 5. Design Pattern — Decorator Pattern (Fitur 1 & 12: Login + OTP)

Mengimplementasikan **Decorator Pattern** untuk autentikasi bertingkat.

### Struktur Kelas:
```
AuthServiceInterface (Interface)
    │
    ├── BasicAuth (Concrete Component)
    │   └── Validasi email + password dari database
    │
    └── AuthDecorator (Abstract Decorator)
        └── OTPDecorator (Concrete Decorator)
            └── Menambahkan layer verifikasi OTP secara transparan
```

### File yang dibuat:
- `app/Contracts/AuthServiceInterface.php` — Interface autentikasi
- `app/Services/Auth/BasicAuth.php` — Validasi username + password
- `app/Services/Auth/AuthDecorator.php` — Abstract decorator
- `app/Services/Auth/OTPDecorator.php` — Concrete decorator (generate & verify OTP)

### Cara kerja:
1. `BasicAuth` memvalidasi email + password terhadap database
2. `OTPDecorator` membungkus `BasicAuth` tanpa mengubah implementasinya
3. Jika OTP aktif: generate kode 6 digit, simpan ke DB, user harus verifikasi
4. Jika OTP tidak aktif: login langsung berhasil

---

## 6. Design Pattern — Abstract Factory (Fitur 1: Role Management)

Mengimplementasikan **Abstract Factory Pattern** untuk pembuatan user berdasarkan role.

### Struktur Kelas:
```
UserFactoryInterface (Interface)
    │
    ├── StudentFactory  → membuat User role 'mahasiswa' + profil Student
    ├── LecturerFactory → membuat User role 'dosen' + profil Lecturer
    └── AdminFactory    → membuat User role 'admin'

UserFactoryManager → menentukan factory berdasarkan role (resolver)
```

### File yang dibuat:
- `app/Contracts/UserFactoryInterface.php` — Interface factory
- `app/Services/User/StudentFactory.php` — Factory mahasiswa
- `app/Services/User/LecturerFactory.php` — Factory dosen
- `app/Services/User/AdminFactory.php` — Factory admin
- `app/Services/User/UserFactoryManager.php` — Resolver factory berdasarkan role

### Cara kerja:
```php
// Menentukan factory berdasarkan role
$factory = UserFactoryManager::getFactory('mahasiswa');

// Membuat user + profil sekaligus
$user = $factory->createUser(
    ['name' => 'Francisco', 'email' => '...', 'password' => '...'],
    ['nim' => '2472040', 'program_studi' => 'TI', ...]
);
```

---

## 7. Design Pattern — Singleton (Fitur 7: Activity Logger)

Mengimplementasikan **Singleton Pattern** untuk pencatatan aktivitas terpusat.

### File yang dibuat:
- `app/Services/ActivityLogger.php`

### Cara kerja:
```php
// Selalu mendapatkan instance yang sama (Singleton)
$logger = ActivityLogger::getInstance();

// Mencatat aktivitas ke tabel activity_logs
$logger->log('LOGIN', $userId, 'users', $userId, ['role' => 'admin']);
```

- Private constructor → tidak bisa di-new dari luar
- Static `getInstance()` → lazy initialization
- Digunakan oleh semua komponen sistem (cross-cutting concern)

---

## 8. Middleware & Routing

### RoleMiddleware
- **File:** `app/Http/Middleware/RoleMiddleware.php`
- Membatasi akses halaman berdasarkan role user
- Penggunaan: `->middleware('role:admin')` atau `->middleware('role:dosen,admin')`
- Didaftarkan di `bootstrap/app.php`

### Routes (`routes/web.php`)
- Guest routes: login, OTP
- Authenticated routes: dashboard, logout
- Role-based groups: `/mahasiswa/*`, `/dosen/*`, `/admin/*` (siap untuk Phase 2)

---

## 9. Controllers

### AuthController (`app/Http/Controllers/AuthController.php`)
- `showLogin()` — menampilkan halaman login
- `login()` — proses login menggunakan Decorator Pattern (BasicAuth → OTPDecorator)
- `showOtp()` — halaman input kode OTP
- `verifyOtp()` — verifikasi kode OTP
- `logout()` — logout + catat ke ActivityLogger

### DashboardController (`app/Http/Controllers/DashboardController.php`)
- `index()` — redirect ke dashboard sesuai role (match expression)
- Dashboard Mahasiswa: statistik enrollment, submission, pending
- Dashboard Dosen: statistik course, assignment
- Dashboard Admin: statistik user, course, recent activity logs

---

## 10. Views (Blade Templates)

### Layout
- `resources/views/layouts/app.blade.php` — Layout utama dengan sidebar navigasi per role, topbar, Bootstrap 5
- `resources/views/layouts/auth.blade.php` — Layout autentikasi (login/OTP) dengan gradient background

### Halaman Auth
- `resources/views/auth/login.blade.php` — Form login email + password
- `resources/views/auth/otp.blade.php` — Form verifikasi kode OTP 6 digit

### Dashboard per Role
- `resources/views/dashboard/student.blade.php` — Statistik + profil mahasiswa
- `resources/views/dashboard/lecturer.blade.php` — Statistik + profil dosen
- `resources/views/dashboard/admin.blade.php` — Statistik + tabel activity log terbaru

---

## 11. Database Seeder

**File:** `database/seeders/SmartCampusSeeder.php`

Menggunakan **Abstract Factory Pattern** (`UserFactoryManager`) untuk membuat data dummy:
- 1 Admin: `admin@smartcampus.ac.id`
- 3 Dosen: Dr. Budi, Dr. Sari, Prof. Ahmad
- 5 Mahasiswa: Francisco, Teofilus, Calvin, Dave, Andi (sesuai anggota kelompok)

Semua password: `password` (di-hash otomatis oleh Laravel)

---

## 12. Testing & Verifikasi

- ✅ `php artisan migrate:fresh --seed` — semua 14 tabel berhasil dibuat, 9 akun dummy terinsert
- ✅ Login sebagai Admin (`admin@smartcampus.ac.id`) — berhasil, redirect ke dashboard admin
- ✅ Activity Logger mencatat aksi LOGIN otomatis ke tabel `activity_logs`
- ✅ Sidebar navigasi menampilkan menu sesuai role

---

## Struktur File yang Dibuat/Dimodifikasi

```
SmartCampus/
├── .env                                          [MODIFIED] MySQL config
├── bootstrap/app.php                             [MODIFIED] Register RoleMiddleware
├── routes/web.php                                [MODIFIED] Auth + dashboard routes
│
├── app/
│   ├── Contracts/
│   │   ├── AuthServiceInterface.php              [NEW] Decorator Pattern
│   │   └── UserFactoryInterface.php              [NEW] Abstract Factory
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php                [NEW] Login/Logout/OTP
│   │   │   └── DashboardController.php           [NEW] Dashboard per role
│   │   └── Middleware/
│   │       └── RoleMiddleware.php                [NEW] Role-based access
│   │
│   ├── Models/
│   │   ├── User.php                              [MODIFIED] +role, +otp, +relasi
│   │   ├── Student.php                           [NEW]
│   │   ├── Lecturer.php                          [NEW]
│   │   ├── Course.php                            [NEW]
│   │   ├── Enrollment.php                        [NEW]
│   │   ├── Assignment.php                        [NEW]
│   │   ├── Submission.php                        [NEW]
│   │   ├── Grade.php                             [NEW]
│   │   ├── Notification.php                      [NEW]
│   │   ├── ActivityLog.php                       [NEW]
│   │   ├── ExportLog.php                         [NEW]
│   │   └── UiPreference.php                      [NEW]
│   │
│   └── Services/
│       ├── ActivityLogger.php                    [NEW] Singleton Pattern
│       ├── Auth/
│       │   ├── BasicAuth.php                     [NEW] Decorator - Component
│       │   ├── AuthDecorator.php                 [NEW] Decorator - Abstract
│       │   └── OTPDecorator.php                  [NEW] Decorator - Concrete
│       └── User/
│           ├── StudentFactory.php                [NEW] Abstract Factory
│           ├── LecturerFactory.php               [NEW] Abstract Factory
│           ├── AdminFactory.php                  [NEW] Abstract Factory
│           └── UserFactoryManager.php            [NEW] Factory Resolver
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php         [MODIFIED]
│   │   ├── 0001_01_01_000003_create_students_table.php      [NEW]
│   │   ├── 0001_01_01_000004_create_lecturers_table.php     [NEW]
│   │   ├── 0001_01_01_000005_create_courses_table.php       [NEW]
│   │   ├── 0001_01_01_000006_create_enrollments_table.php   [NEW]
│   │   ├── 0001_01_01_000007_create_assignments_table.php   [NEW]
│   │   ├── 0001_01_01_000008_create_submissions_table.php   [NEW]
│   │   ├── 0001_01_01_000009_create_grades_table.php        [NEW]
│   │   ├── 0001_01_01_000010_create_notifications_table.php [NEW]
│   │   ├── 0001_01_01_000011_create_activity_logs_table.php [NEW]
│   │   ├── 0001_01_01_000012_create_export_logs_table.php   [NEW]
│   │   └── 0001_01_01_000013_create_ui_preferences_table.php[NEW]
│   └── seeders/
│       ├── DatabaseSeeder.php                               [MODIFIED]
│       └── SmartCampusSeeder.php                            [NEW]
│
└── resources/views/
    ├── layouts/
    │   ├── app.blade.php                         [NEW] Main layout + sidebar
    │   └── auth.blade.php                        [NEW] Auth layout
    ├── auth/
    │   ├── login.blade.php                       [NEW] Halaman login
    │   └── otp.blade.php                         [NEW] Halaman OTP
    └── dashboard/
        ├── student.blade.php                     [NEW] Dashboard mahasiswa
        ├── lecturer.blade.php                    [NEW] Dashboard dosen
        └── admin.blade.php                       [NEW] Dashboard admin
```

---

## TODO — Week 2

- [ ] Halaman Activity Log lengkap (filter user, aksi, tanggal) — Fitur 7
- [ ] Polish OTP flow end-to-end — Fitur 12
- [ ] Integrasi dengan fitur anggota lain
