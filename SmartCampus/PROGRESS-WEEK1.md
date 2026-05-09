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

Semua password: `password` (di-hash otomatis oleh Laravel)pull 

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

---
---

<!-- ══════════════════════════════════════════════════════════════════════════════ -->
<!-- FITUR MANAJEMEN TUGAS (CRUD) — Dibuat oleh Teofilus Juan Puapadang (2472053) -->
<!-- Branch: Week--1-1---Teofilus Juan Puapadang                                 -->
<!-- ══════════════════════════════════════════════════════════════════════════════ -->

# PROGRESS — Fitur Manajemen Tugas (CRUD)
**Dibuat oleh:** Teofilus Juan Puapadang (2472053)  
**Branch:** `Week--1-1---Teofilus Juan Puapadang`  
**Tanggal:** 9 Mei 2026  

---

## 1. Gambaran Umum Fitur

Fitur **Manajemen Tugas (CRUD)** memungkinkan dosen untuk membuat, melihat, mengedit, dan menghapus tugas perkuliahan. Fitur ini dibangun menggunakan **Command Pattern** sebagai pola desain utama untuk mengenkapsulasi setiap operasi CRUD menjadi objek command yang independen.

### Apa yang bisa dilakukan:
- **Dosen:** Membuat tugas baru, melihat detail tugas + daftar submission mahasiswa, mengedit tugas, dan menghapus tugas (Full CRUD)
- **Mahasiswa:** Melihat daftar tugas dari mata kuliah yang di-enroll, melihat detail tugas, dan mengumpulkan file tugas (submission)
- **Admin:** Melihat semua tugas dari seluruh mata kuliah (read-only)

---

## 2. Design Pattern — Command Pattern (Fitur: Manajemen Tugas)

Mengimplementasikan **Command Pattern** untuk operasi CRUD pada tugas/assignment.

### Mengapa Command Pattern?
- **Enkapsulasi:** Setiap operasi (Create/Edit/Delete) dibungkus menjadi objek command tersendiri
- **Audit Trail:** Invoker secara otomatis mencatat setiap operasi ke `ActivityLogger` (Singleton)
- **Undo/Redo Ready:** Setiap command menyimpan data sebelum/sesudah perubahan (snapshot)
- **Single Responsibility:** Controller tidak perlu menangani logika bisnis dan logging secara langsung

### Struktur Kelas Command Pattern:
```
TaskCommandInterface (Interface)
    │
    ├── CreateTaskCommand    → Enkapsulasi operasi CREATE assignment
    ├── EditTaskCommand      → Enkapsulasi operasi UPDATE assignment (+ snapshot before/after)
    └── DeleteTaskCommand    → Enkapsulasi operasi DELETE assignment (+ snapshot preservation)

TaskCommandInvoker (Invoker)
    └── Menjalankan command + mencatat ke ActivityLogger (Singleton) secara otomatis

AssignmentController (Client)
    └── Membuat command objects dan mengirim ke Invoker
```

### File yang dibuat:
- `app/Contracts/TaskCommandInterface.php` — Interface dengan 5 method kontrak
- `app/Services/Task/CreateTaskCommand.php` — Concrete command untuk membuat tugas
- `app/Services/Task/EditTaskCommand.php` — Concrete command untuk mengedit tugas
- `app/Services/Task/DeleteTaskCommand.php` — Concrete command untuk menghapus tugas
- `app/Services/Task/TaskCommandInvoker.php` — Invoker yang menjalankan command + logging

### Interface TaskCommandInterface:
```php
interface TaskCommandInterface
{
    public function execute(): mixed;        // Menjalankan operasi CRUD
    public function getAction(): string;     // Nama aksi: CREATE/UPDATE/DELETE
    public function getDetail(): array;      // Data audit trail (before/after)
    public function getTargetTable(): string; // Tabel target: 'assignments'
    public function getTargetId(): ?int;     // ID record yang dioperasikan
}
```

### Cara kerja Command Pattern di Controller:

**CREATE — Membuat tugas baru:**
```php
// 1. Buat command object dengan data tervalidasi
$command = new CreateTaskCommand($validated);

// 2. Kirim ke Invoker → eksekusi + logging otomatis
$assignment = $this->invoker->execute($command, Auth::id());
```

**EDIT — Mengedit tugas:**
```php
// 1. Buat command (otomatis menyimpan snapshot data sebelum perubahan)
$command = new EditTaskCommand($assignment, $validated);

// 2. Kirim ke Invoker → update + simpan data before/after ke log
$this->invoker->execute($command, Auth::id());
```

**DELETE — Menghapus tugas:**
```php
// 1. Buat command (otomatis menyimpan snapshot lengkap sebelum hapus)
$command = new DeleteTaskCommand($assignment);

// 2. Kirim ke Invoker → hapus + simpan snapshot ke log
$this->invoker->execute($command, Auth::id());
```

### Integrasi dengan Singleton Pattern (ActivityLogger):
Setiap kali Invoker menjalankan command, aktivitas dicatat otomatis:
```php
// Di dalam TaskCommandInvoker::execute()
ActivityLogger::getInstance()->log(
    action: $command->getAction(),       // 'CREATE_ASSIGNMENT', 'UPDATE_ASSIGNMENT', 'DELETE_ASSIGNMENT'
    userId: $userId,
    targetTable: $command->getTargetTable(), // 'assignments'
    targetId: $command->getTargetId(),
    detail: $command->getDetail()         // Data snapshot untuk audit trail
);
```

---

## 3. Controller & Routing

### AssignmentController (`app/Http/Controllers/AssignmentController.php`)
Controller ini bertindak sebagai **Client** dalam Command Pattern:
- `index()` — Menampilkan daftar tugas (dengan search & filter, role-aware)
- `show()` — Menampilkan detail tugas + daftar submission mahasiswa
- `create()` — Menampilkan form pembuatan tugas baru
- `store()` — Menyimpan tugas baru menggunakan `CreateTaskCommand`
- `edit()` — Menampilkan form edit tugas
- `update()` — Mengupdate tugas menggunakan `EditTaskCommand`
- `destroy()` — Menghapus tugas menggunakan `DeleteTaskCommand`

### SubmissionController (`app/Http/Controllers/SubmissionController.php`)
- `store()` — Menerima file submission dari mahasiswa
- Validasi format file dan ukuran sesuai pengaturan tugas
- Otomatis menentukan status: `submitted` (tepat waktu) atau `late` (terlambat)

### Routes (`routes/web.php`):
```php
// Dosen — Full CRUD
Route::prefix('dosen')->middleware('role:dosen')->group(function () {
    Route::resource('assignments', AssignmentController::class);
});

// Mahasiswa — Read + Submit
Route::prefix('mahasiswa')->middleware('role:mahasiswa')->group(function () {
    Route::get('assignments', [AssignmentController::class, 'index']);
    Route::get('assignments/{assignment}', [AssignmentController::class, 'show']);
    Route::post('assignments/{assignment}/submit', [SubmissionController::class, 'store']);
});

// Admin — Read only
Route::prefix('admin')->middleware('role:admin')->group(function () {
    Route::get('assignments', [AssignmentController::class, 'index']);
    Route::get('assignments/{assignment}', [AssignmentController::class, 'show']);
});
```

---

## 4. Views (Blade Templates)

### Halaman yang dibuat:
| File | Deskripsi |
|------|-----------|
| `resources/views/assignments/index.blade.php` | Daftar tugas dengan search, filter MK, filter status deadline |
| `resources/views/assignments/show.blade.php` | Detail tugas + tabel submission mahasiswa + form upload (mahasiswa) |
| `resources/views/assignments/create.blade.php` | Form buat tugas baru (dropdown MK, judul, deskripsi, deadline, skor, format file) |
| `resources/views/assignments/edit.blade.php` | Form edit tugas (pre-filled dengan data existing) |

### Fitur UI:
- **Badge Status Deadline:** 🟢 Aktif (>3 hari), 🟡 Mendekati (≤3 hari), 🔴 Terlambat (lewat deadline)
- **Search & Filter:** Cari judul tugas, filter berdasarkan mata kuliah, filter status deadline
- **Modal Konfirmasi Hapus:** Konfirmasi sebelum menghapus tugas
- **Validasi Error:** Pesan error ditampilkan langsung di form
- **Role-aware:** Tombol CRUD hanya muncul untuk dosen, mahasiswa hanya bisa lihat & submit

### Layout Sidebar (Modifikasi):
File `resources/views/layouts/app.blade.php` dimodifikasi untuk menambahkan navigasi:
- **Dosen:** Dashboard, Kelola Tugas, Penilaian, Monitor Mahasiswa, Export Laporan
- **Mahasiswa:** Dashboard, Tugas Saya, Nilai Saya
- **Admin:** Dashboard, Semua Tugas, Manajemen User, Laporan Aktivitas

---

## 5. Clean Code & Defensive Programming

### Guard Clauses yang diterapkan:
```php
// Hanya pemilik tugas yang bisa edit/hapus
private function authorizeOwnership($user, Assignment $assignment): void
{
    if ($assignment->created_by !== $user->id) {
        abort(403, 'Anda tidak memiliki akses untuk mengelola tugas ini.');
    }
}

// Mahasiswa hanya bisa akses tugas dari enrolled courses
private function authorizeStudentAccess($user, Assignment $assignment): void
{
    $isEnrolled = $user->student->enrollments()
        ->where('course_id', $assignment->course_id)
        ->where('status', 'active')
        ->exists();

    if (!$isEnrolled) {
        abort(403, 'Anda tidak terdaftar di mata kuliah ini.');
    }
}
```

### DRY Principle:
- Method `validateAssignment()` dipakai bersama oleh `store()` dan `update()`
- Method `applyRoleFilter()` memisahkan logika query per role
- Method `getCoursesForUser()` dan `getDosenCourses()` untuk reusable query

---

## 6. Database Seeder (Update)

**File:** `database/seeders/SmartCampusSeeder.php`

### Data yang ditambahkan:

**7 Dosen (1 dosen = 1 mata kuliah):**
| Dosen | Email | Mata Kuliah | Kode |
|-------|-------|-------------|------|
| Dr. Budi Santoso | budi@smartcampus.ac.id | Pola Desain Perangkat Lunak | IN235 |
| Dr. Sari Dewi | sari@smartcampus.ac.id | Web Dasar | IN212 |
| Prof. Ahmad Wijaya | ahmad@smartcampus.ac.id | Pancasila | MK017 |
| Dr. Dewi Lestari | dewi@smartcampus.ac.id | Statistika | IN241 |
| Dr. Rizki Ramadhan | rizki@smartcampus.ac.id | Kecerdasan Mesin | IN242 |
| Dr. Maya Putri | maya@smartcampus.ac.id | Proyek Perangkat Lunak | IN254 |
| Dr. Hendra Kusuma | hendra@smartcampus.ac.id | Strategi Algoritmik | IN244 |

**8 Tugas (variasi deadline):**
| No | Judul Tugas | Mata Kuliah | Status |
|----|-------------|-------------|--------|
| 1 | Implementasi Singleton Pattern | IN235 PDPL | 🔴 Terlambat |
| 2 | Landing Page dengan HTML & CSS | IN212 Web Dasar | 🔴 Terlambat |
| 3 | Command Pattern pada CRUD System | IN235 PDPL | 🟡 Mendekati |
| 4 | Implementasi Linear Regression | IN242 Kecerdasan Mesin | 🟡 Mendekati |
| 5 | Tugas Besar: Smart Campus | IN235 PDPL | 🟢 Aktif |
| 6 | Analisis Data Deskriptif | IN241 Statistika | 🟢 Aktif |
| 7 | Dynamic Programming - Knapsack | IN244 Strategi Algoritmik | 🟢 Aktif |
| 8 | Proposal Proyek Perangkat Lunak | IN254 Proyek PL | 🟢 Aktif |

**8 Dummy Submissions:**
- Tugas 1 (Singleton): 3 submission (Francisco ✅, Juan ✅, Calvin 🔴 Late)
- Tugas 2 (Landing Page): 2 submission (Juan ✅, Andi 🔴 Late)
- Tugas 3 (Command Pattern): 2 submission (Francisco ✅, Juan ✅)
- Tugas 5 (Smart Campus): 1 submission (Juan ✅)

**Password semua akun:** `password`

---

## 7. Alur Penggunaan Fitur

### Alur Dosen — Buat Tugas Baru:
```
Login (budi@smartcampus.ac.id)
  → Dashboard Dosen (lihat statistik MK & tugas)
  → Klik "Kelola Tugas" di sidebar
  → Halaman Daftar Tugas (filter & search)
  → Klik "+ Buat Tugas Baru"
  → Isi form (Mata Kuliah, Judul, Deskripsi, Deadline, Skor, Format File, Ukuran Maks)
  → Klik "Simpan Tugas"
  → [CreateTaskCommand dieksekusi via Invoker + logging otomatis]
  → Redirect ke halaman Detail Tugas + pesan sukses
```

### Alur Dosen — Edit Tugas:
```
Halaman Daftar Tugas
  → Klik ikon ✏️ Edit pada tugas yang diinginkan
  → Form edit tampil (pre-filled dengan data existing)
  → Ubah data yang diinginkan
  → Klik "Perbarui Tugas"
  → [EditTaskCommand dieksekusi via Invoker + snapshot before/after]
  → Redirect ke Detail Tugas + pesan sukses
```

### Alur Dosen — Hapus Tugas:
```
Halaman Daftar Tugas
  → Klik ikon 🗑️ Hapus pada tugas
  → Modal konfirmasi muncul: "Apakah Anda yakin?"
  → Klik "Hapus"
  → [DeleteTaskCommand dieksekusi via Invoker + snapshot preservation]
  → Redirect ke Daftar Tugas + pesan sukses
```

### Alur Mahasiswa — Lihat & Submit Tugas:
```
Login (juan@student.ac.id)
  → Dashboard Mahasiswa
  → Klik "Tugas Saya" di sidebar
  → Daftar tugas dari mata kuliah yang di-enroll
  → Klik judul tugas untuk lihat detail
  → Upload file tugas (validasi format & ukuran)
  → Status: "Submitted" (tepat waktu) atau "Late" (terlambat)
```

---

## 8. Struktur File yang Dibuat/Dimodifikasi

```
SmartCampus/
├── app/
│   ├── Contracts/
│   │   └── TaskCommandInterface.php              [NEW] Command Pattern Interface
│   │
│   ├── Http/Controllers/
│   │   ├── AssignmentController.php              [NEW] CRUD + Command Pattern Client
│   │   └── SubmissionController.php              [NEW] File submission handling
│   │
│   └── Services/Task/
│       ├── CreateTaskCommand.php                 [NEW] Concrete Command — Create
│       ├── EditTaskCommand.php                   [NEW] Concrete Command — Edit
│       ├── DeleteTaskCommand.php                 [NEW] Concrete Command — Delete
│       └── TaskCommandInvoker.php                [NEW] Invoker + ActivityLogger
│
├── database/seeders/
│   └── SmartCampusSeeder.php                     [MODIFIED] +7 courses, +4 dosen, +8 assignments, +8 submissions
│
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php                         [MODIFIED] +sidebar navigasi per role
│   └── assignments/
│       ├── index.blade.php                       [NEW] Daftar tugas + search + filter
│       ├── show.blade.php                        [NEW] Detail tugas + submission table
│       ├── create.blade.php                      [NEW] Form buat tugas baru
│       └── edit.blade.php                        [NEW] Form edit tugas
│
└── routes/
    └── web.php                                   [MODIFIED] +routes CRUD assignment & submission
```

---

## 9. Testing & Verifikasi

- ✅ `php artisan migrate:fresh --seed` — 14 tabel berhasil, 13 akun + 7 courses + 8 tugas + 8 submissions
- ✅ Login sebagai Dosen (`budi@smartcampus.ac.id`) — Dashboard menampilkan 1 MK diampu, 3 tugas
- ✅ Halaman Kelola Tugas — Menampilkan 3 tugas milik Dr. Budi dengan badge status benar
- ✅ Detail Tugas — Menampilkan 3 submission mahasiswa (Francisco, Juan, Calvin)
- ✅ Form Buat Tugas Baru — Dropdown hanya 1 MK: IN235 Pola Desain Perangkat Lunak
- ✅ Command Pattern berjalan — `CreateTaskCommand`, `EditTaskCommand`, `DeleteTaskCommand` terintegrasi
- ✅ ActivityLogger mencatat semua operasi CRUD secara otomatis via Invoker
- ✅ Guard Clauses — Dosen hanya bisa kelola tugas miliknya, mahasiswa hanya lihat enrolled courses
- ✅ Tidak ada error pada semua halaman yang diuji

---

## 10. Ringkasan Design Pattern yang Digunakan

| Design Pattern | Komponen | Fungsi |
|----------------|----------|--------|
| **Command Pattern** | `CreateTaskCommand`, `EditTaskCommand`, `DeleteTaskCommand`, `TaskCommandInvoker` | Mengenkapsulasi operasi CRUD sebagai objek command |
| **Singleton Pattern** | `ActivityLogger` | Pencatatan aktivitas terpusat (digunakan oleh Invoker) |
| **Abstract Factory** | `UserFactoryManager` | Pembuatan akun dosen (7 dosen) melalui `LecturerFactory` |

---
