# 📋 Master To-Do List: Proyek SmartCampus

Berikut adalah rancangan tugas lengkap dari awal hingga akhir (selesai) untuk seluruh anggota kelompok. Tugas yang sudah selesai akan ditandai dengan centang (✅).

---

## 🏗️ FASE 1: FONDASI PROYEK (Selesai ✅)
Tugas-tugas dasar infrastruktur yang telah disiapkan di awal project agar semua anggota bisa bekerja dengan lancar.

- [x] Inisialisasi Project Laravel 12
- [x] Konfigurasi `.env` dan Database MySQL
- [x] Pembuatan 12 file Migrations (struktur tabel lengkap sesuai ERD)
- [x] Pembuatan 12 Model Eloquent beserta relasinya
- [x] Pembuatan layout dasar Blade (app, auth, dashboard)
- [x] Pembuatan Seeder (dummy data: admin, dosen, mahasiswa, courses, assignments, submissions)
- [x] Setup Middleware Role-based (`RoleMiddleware`)
- [x] Setup Routes dasar (login, OTP, dashboard, role-based groups)
- [x] Pembuatan `SETUP-GUIDE.md` untuk panduan clone/pull bagi anggota tim
- [x] Pembuatan `README.md` berisi penjelasan Design Pattern per fitur
- [x] Pembuatan `TODO-LIST-PROJECT.md` untuk tracking progress keseluruhan

---

## 👨‍💻 FASE 2: FITUR UTAMA (Berdasarkan Pembagian Tugas)

### 👤 Francisco Valentino (Fitur 1, 7, 12)

- **Fitur 1: Login & Role Management** (Design Pattern: *Abstract Factory* & *Decorator*)
  - [x] `UserFactoryInterface` — Interface Abstract Factory
  - [x] `StudentFactory`, `LecturerFactory`, `AdminFactory` — Concrete Factory per role
  - [x] `UserFactoryManager` — Factory resolver berdasarkan role
  - [x] `AuthServiceInterface` — Interface Decorator Pattern
  - [x] `BasicAuth` — Concrete Component (validasi email+password)
  - [x] `AuthDecorator` — Abstract Decorator
  - [x] `AuthController` — Login, logout, OTP flow
  - [x] `DashboardController` — Routing ke dashboard per role
  - [x] `RoleMiddleware` — Pembatasan akses berdasarkan role
  - [x] View: `login.blade.php` — Halaman login
  - [x] View: `dashboard/student.blade.php`, `lecturer.blade.php`, `admin.blade.php`
  - [x] Sidebar navigasi per role di `layouts/app.blade.php`

- **Fitur 12: Autentikasi Bertingkat (OTP)** (Design Pattern: *Decorator*)
  - [x] `OTPDecorator` — Concrete Decorator (generate & verifikasi OTP)
  - [x] Kolom `otp_enabled` di tabel users (migration baru)
  - [x] Method `isOtpRequired()` mengecek kolom `otp_enabled`
  - [x] Simulasi pengiriman OTP (log + session untuk development mode)
  - [x] View: `otp.blade.php` — Halaman input OTP dengan tampilan kode di dev mode
  - [x] Admin di-seed dengan `otp_enabled = true` untuk demo presentasi
  - [x] Alur end-to-end: Login → OTP Required → Tampil kode → Verifikasi → Dashboard

- **Fitur 7: Riwayat Aktivitas / Activity Log** (Design Pattern: *Singleton*)
  - [x] `ActivityLogger` class menggunakan Singleton Pattern (`getInstance()`)
  - [x] Method `log()` dan `getLogs()` dengan filter
  - [x] Integrasi otomatis ke AuthController (LOGIN, LOGIN_OTP, LOGOUT)
  - [x] Integrasi ke TaskCommandInvoker (CREATE, UPDATE, DELETE assignment)
  - [x] `ActivityLogController` — Controller dengan filter user/action/tanggal
  - [x] View: `activity-logs/index.blade.php` — Tabel log + filter + modal detail JSON
  - [x] View: `activity-logs/show.blade.php` — Detail satu entri log
  - [x] Routes Activity Log untuk semua role (admin, dosen, mahasiswa)
  - [x] Sidebar link "Riwayat Aktivitas" untuk semua role

---

### 👤 Ko Dev — Dave Andrew (Fitur 3, 5, 11)

- **Fitur 3: Deadline Reminder Otomatis** (Design Pattern: *Observer*)
  - [x] `DeadlineSubjectInterface` — Interface Subject
  - [x] `DeadlineObserverInterface` — Interface Observer
  - [x] Setup Laravel Scheduler / Cronjob
  - [x] Logika pengecekan tugas mendekati deadline (H-3, H-1)
  - [x] Trigger alert/notifikasi untuk mahasiswa

- **Fitur 5: Sistem Penilaian Otomatis** (Design Pattern: *Strategy*)
  - [ ] `GradingStrategyInterface` — Interface Strategy
  - [ ] `NumericGrading`, `LetterGrading`, `PassFailGrading` — Concrete Strategies
  - [ ] `GradeController` — Controller penilaian
  - [ ] View: halaman input/edit nilai oleh dosen
  - [ ] View: penampilan nilai di dashboard mahasiswa

- **Fitur 11: Export Data (PDF/CSV)** (Design Pattern: *Strategy*)
  - [ ] `ExportStrategyInterface` — Interface Strategy
  - [ ] `PDFExport`, `CSVExport` — Concrete Strategies
  - [ ] Setup library export (dompdf / maatwebsite excel)
  - [ ] `ExportController` — Controller export
  - [ ] View: halaman pilih format export

---

### 👤 Juan — Teofilus Juan Puapadang (Fitur 2, 8, 10)

- **Fitur 2: Manajemen Tugas / CRUD** (Design Pattern: *Command Pattern*)
  - [x] `TaskCommandInterface` — Interface Command
  - [x] `CreateTaskCommand`, `EditTaskCommand`, `DeleteTaskCommand` — Concrete Commands
  - [x] `TaskCommandInvoker` — Invoker + logging otomatis via ActivityLogger
  - [x] `AssignmentController` — CRUD tugas (Client)
  - [x] `SubmissionController` — Upload file tugas
  - [x] View: `assignments/index.blade.php` — Daftar tugas + search + filter
  - [x] View: `assignments/show.blade.php` — Detail tugas + submission table
  - [x] View: `assignments/create.blade.php` — Form buat tugas
  - [x] View: `assignments/edit.blade.php` — Form edit tugas

- **Fitur 8: Fitur Undo / Redo** (Design Pattern: *Command* / *Memento*)
  - [ ] Integrasi dengan Command Pattern dari CRUD Tugas
  - [ ] Stack riwayat command untuk Undo/Redo
  - [ ] View: tombol Undo bagi dosen (contoh: batal hapus tugas)

- **Fitur 10: Notifikasi MultiChannel** (Design Pattern: *Observer / Factory Method*)
  - [ ] `NotificationChannelInterface` — Interface
  - [ ] `EmailChannel`, `DatabaseChannel` — Concrete Channels
  - [ ] `NotificationFactory` — Factory Method untuk memilih channel
  - [ ] Dropdown notifikasi lonceng di Topbar
  - [ ] Controller dan View notifikasi

---

### 👤 Ko Calvin — Calvin Yohanis (Fitur 4, 6, 9)

- **Fitur 4: Tracking Progress Tugas** (Design Pattern: *State*)
  - [x] `SubmissionStateInterface` — Interface State
  - [x] `DraftState`, `SubmittedState`, `GradedState` — Concrete States
  - [x] State Machine (Not Started → In Progress → Submitted → Graded)
  - [x] Progress bar di halaman detail tugas mahasiswa

- **Fitur 6: Penyimpanan Data File** (Design Pattern: *Strategy*)
  - [ ] `StorageStrategyInterface` — Interface Strategy
  - [ ] `LocalStorageStrategy` — Concrete Strategy (local disk)
  - [ ] Konfigurasi upload file & validasi format
  - [ ] *(Opsional)* `CloudStorageStrategy` untuk AWS S3

- **Fitur 9: Mode Tampilan (Light/Dark)** (Design Pattern: *Abstract Factory*)
  - [ ] `UIThemeFactoryInterface` — Interface Abstract Factory
  - [ ] `LightThemeFactory`, `DarkThemeFactory` — Concrete Factories
  - [ ] Toggle tema di UI (Topbar)
  - [ ] Penyimpanan preferensi tema di tabel `ui_preferences`
  - [ ] Penyesuaian CSS / Bootstrap variables

---

## 🚀 FASE 3: INTEGRASI & FINISHING
- [ ] Penggabungan semua branch fitur ke dalam branch `main`
- [ ] Pengetesan integrasi antar fitur (contoh: Submission memicu Grading lalu Notifikasi dan Log)
- [ ] Perbaikan Bugs & Error Handling
- [ ] Finalisasi User Interface (UI Polish & konsistensi antar halaman)
- [ ] Testing end-to-end semua alur pengguna (Admin, Dosen, Mahasiswa)
- [ ] Dokumentasi Akhir Proyek / Laporan Tubes
- [ ] Persiapan Presentasi (demo alur & penjelasan Design Pattern)

---
*Terakhir diperbarui: 23 Mei 2026*