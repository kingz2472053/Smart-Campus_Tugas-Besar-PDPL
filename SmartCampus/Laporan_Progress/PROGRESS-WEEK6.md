# 📈 LAPORAN PROGRES TOTAL — SmartCampus

**Mata Kuliah:** Pola Desain Perangkat Lunak  
**Tim Pengembang:**

1. Dave Andrew (2172015)
2. Francisco Valentino (2472040)
3. Teofilus Juan Puapadang (2472053)
4. Calvin Yohanis (2272017)

---

# PROGRESS WEEK 6 — SmartCampus

**Anggota:** Dave Andrew (2172015)  
**Branch:** `Week-6---Dave-Andrew`  
**Tanggal:** 12 Juni 2026

## 1. Gambaran Umum Fitur

Pada minggu ke-6, fokus pengembangan adalah membangun antarmuka terpadu untuk mata kuliah mahasiswa, implementasi fitur unduh nilai (F6), dan peningkatan validasi kemananan sistem pada panel kontrol dosen.

## 2. Design Pattern — Strategy Pattern (Fitur 6: Export Data)

Mengimplementasikan **Strategy Pattern** untuk menangani format keluaran data secara fleksibel dan efisien tanpa membebani server.

-   `ExportStrategyInterface`: Antarmuka standar eksekusi ekspor data.
-   `PdfExportStrategy`: Memanfaatkan `barryvdh/laravel-dompdf` untuk mencetak PDF laporan berformat rapi.
-   `CsvExportStrategy`: Memanfaatkan `StreamedResponse` bawaan Laravel dengan fungsi `fputcsv` untuk menghasilkan file CSV instan dengan alokasi RAM 0%.

## 3. Implementasi Fitur UI Mata Kuliah Mahasiswa

Pembuatan halaman khusus agar alur peninjauan nilai oleh mahasiswa menjadi logis dan intuitif.

-   Dibuat `CourseController` dengan method `index` (Daftar Mata Kuliah) dan `grades` (Detail Nilai).
-   Pembuatan Blade Views baru di folder `resources/views/courses/`.
-   Tombol _Export PDF_ dan _Export CSV_ ditempatkan strategis di halaman rekap nilai.

## 4. Keamanan Sistem (Guard Clauses) & Bug Fixes

Penyempurnaan validasi form (frontend/backend) untuk menutup celah _human-error_:

-   **Default Deadline:** Secara otomatis di-set H+1 (24 Jam) menggunakan `now()->addDay()` saat formulir buat tugas diakses.
-   **Kunci Skor:** Mengubah field `max_score` menjadi _readonly_ pada saat edit tugas untuk mencegah modifikasi nilai sepihak.
-   **Validasi Penilaian:** Mencegah dosen menginput nilai mentah (`raw_score`) yang melebihi batas angka `max_score` tugas secara spesifik di controller dan UI modal.
-   **Hotfix: Duplicate Command:** Menghapus eksekusi duplikat antara _Repository_ dan _Command Invoker_ di dalam `AssignmentController@store` sehingga tugas tidak tersimpan dua kali.
-   **Hotfix: Tailwind Clash:** Menerapkan `Paginator::useBootstrapFive()` pada `AppServiceProvider` untuk memperbaiki _bug_ visual tombol pagination raksasa bawaan Laravel 12.

## 5. Struktur File yang Dibuat/Dimodifikasi

```text
SmartCampus/
├── app/
│   ├── Contracts/
│   │   └── ExportStrategyInterface.php    [NEW]
│   ├── Http/Controllers/
│   │   ├── ExportController.php           [NEW] Client context untuk strategi ekspor
│   │   ├── CourseController.php           [NEW] Menangani halaman MK mahasiswa
│   │   ├── AssignmentController.php       [MODIFIED] Hotfix double execution
│   │   └── SubmissionController.php       [MODIFIED] Backend max_score guard
│   ├── Providers/
│   │   └── AppServiceProvider.php         [MODIFIED] Bootstrap 5 paginator fix
│   └── Services/Export/
│       ├── PdfExportStrategy.php          [NEW] DOMPDF implementation
│       └── CsvExportStrategy.php          [NEW] StreamedResponse implementation
│
├── resources/views/
│   ├── assignments/
│   │   ├── create.blade.php               [MODIFIED] Default deadline H+1
│   │   ├── edit.blade.php                 [MODIFIED] Lock max_score input
│   │   └── show.blade.php                 [MODIFIED] Dynamic max_score in modal
│   ├── courses/
│   │   ├── index.blade.php                [NEW] Grid UI mata kuliah mahasiswa
│   │   └── grades.blade.php               [NEW] Tabel detail nilai + Tombol export
│   └── exports/
│       └── grades_pdf.blade.php           [NEW] Tampilan cetak PDF HTML mentah
│
└── routes/
    └── web.php                            [MODIFIED] Tambahan route export & courses
```

---

# PROGRESS WEEK 6 — SmartCampus

**Anggota:** Francisco Valentino (2472040)  
**Branch:** `Week-6---Francisco-Valentino`  
**Tanggal:** 13 Juni 2026

## 1. Perbaikan Bug Pembuatan Akun & ID Dosen
- **Masalah:** Saat pembuatan akun Mahasiswa/Dosen dari sisi Admin, tabel `students` membutuhkan data tambahan seperti `program_studi` dan `angkatan`, sedangkan dosen membutuhkan `nip` / `nidn`. Data-data ini awalnya tidak ada di formulir.
- **Solusi:** Saya telah menambahkan *input fields* dinamis menggunakan Javascript pada view `admin.users.create` yang akan muncul sesuai dengan role yang dipilih (Dosen / Mahasiswa).
- **Hasil:** Admin sekarang dapat mengisi "Program Studi" dan "Angkatan" saat membuat akun mahasiswa, serta "NIP" saat membuat akun dosen. Bug *constraint violation* di database telah teratasi.

## 2. Fitur Aktivasi/Nonaktifkan Akun
- **Solusi:** 
  - Saya telah menambahkan kolom `Status` (*badge* Aktif/Nonaktif) dan tombol _Toggle_ di tabel manajemen user.
  - Saya menambahkan middleware `CheckIsActive` yang secara otomatis akan melakukan _logout_ paksa pada pengguna jika status `is_active` mereka diubah menjadi `false` oleh Admin.

## 3. Manajemen Mata Kuliah & Kelas Dinamis
- **Solusi:** 
  - Sesuai diskusi tim, tabel `courses` telah dimodifikasi (migration) dengan menambahkan kolom `class_name` (Kelas) dan `academic_year` (Tahun Ajaran), serta menghapus pembatasan `unique` pada satu kode mata kuliah.
  - Pada halaman Tambah Mata Kuliah, kini menggunakan form dinamis. Admin cukup mengisi informasi dasar (Kode, Nama, SKS, Semester, Tahun Ajaran) sekali saja, dan dapat menekan tombol **Tambah Kelas** berkali-kali untuk menentukan Kelas (A, B, C, dst.) beserta Dosen yang mengajar kelas tersebut.

## 4. Pengumuman Global
- **Solusi:** 
  - Telah dibuat tabel dan model `Announcement`.
  - Admin kini memiliki menu baru di _sidebar_ bernama **Pengumuman**, di mana Admin bisa membuat, mengedit, atau menonaktifkan pengumuman penting.
  - Setiap pengumuman yang aktif akan otomatis muncul di bagian atas _Dashboard_ semua user (Admin, Dosen, Mahasiswa) dengan desain _alert box_ yang rapi.

## 5. Pembaruan Fitur & UI Admin
- **Solusi & Penyesuaian:**
  - **Dashboard Admin:** Menyesuaikan metrik statistik menjadi 4 data spesifik (Total Mahasiswa Aktif, Total Dosen, Total Matkul Berjalan, Total Kelas) dan menghapus tabel Aktivitas Terbaru agar antarmuka lebih fokus.
  - **Manajemen Pengguna:** Menghapus _tab filter_ "Semua" dan "Admin" sehingga hanya menampilkan Mahasiswa dan Dosen. Menyembunyikan form "Program Studi" (otomatis terisi *default*) dan mencegah pembuatan akun dengan *role* Admin.
  - **Pencegahan Cache:** Menambahkan *middleware* `PreventBackHistory` untuk mencegah pengguna kembali ke halaman *dashboard* dengan tombol *Back* setelah *logout*.
  - **UI/UX Tambahan:** Menghilangkan peringatan *autofill password* dari *browser* di form *login*, serta membersihkan menu-menu *sidebar* yang tidak diperlukan (seperti Riwayat Aktivitas untuk Dosen/Mahasiswa dan Rekap Nilai untuk Mahasiswa).
  - **Perbaikan Bug Unduh Tugas:** Menambahkan rute dan sistem aman agar Dosen bisa mengunduh file tugas (*submission*) milik mahasiswa, dan mahasiswa bisa mengunduh file yang mereka kumpulkan sendiri.

---

# PROGRESS WEEK 6 — SmartCampus

**Anggota:** Calvin Yohanis (2272017)  
**Branch:** `Week-6---Calvin-Yohanis`  
**Tanggal:** 13 Juni 2026

## 1. Gambaran Umum Fitur

Pada **Week 6**, fokus pengembangan diarahkan pada **Stabilisasi Antarmuka (UI/UX Optimization)**, khususnya integrasi visual *Dark Mode* secara menyeluruh pada komponen tabel riwayat data sistem menggunakan penegasan gaya (*style enforcement*). Langkah ini krusial untuk memastikan komponen pemantauan keamanan tetap memiliki tingkat keterbacaan (*readability*) yang tinggi di berbagai kondisi pencahayaan.

### Apa yang dilakukan:
- **Sinkronisasi Skema Warna Mode Gelap:** Memperbaiki degradasi warna teks pada komponen tabel Bootstrap agar kontras elemen anak (*child nodes*) tetap terjaga saat menggunakan tema gelap.
- **Peningkatan Kontras Alamat IP via `!important`:** Menerapkan penegasan gaya secara agresif pada data Alamat IP (*IP Address*) pelacak aktivitas untuk mempermudah audit keamanan oleh Administrator.
- **Refactoring Arsitektur CSS Tampilan:** Menyusun ulang aturan pewarnaan kustom agar terisolasi dengan baik dan tidak terdistorsi oleh utilitas class bawaan framework.

## 2. Design Pattern & Refactoring — UI Style Enforcement

Untuk menjamin konsistensi tampilan pada *Dark Mode*, dilakukan penerapan aturan penegasan gaya menggunakan properti deklaratif guna memastikan tidak ada kebocoran warna teks dari class bawaan Bootstrap.

### Mengapa Menggunakan Deklarasi `!important` pada Komponen ini?
- **Pencegahan Override Otomatis:** Memastikan bahwa class utilitas bawaan framework tidak menimpa warna teks kustom saat beralih ke mode gelap.
- **Konsistensi Visual Keamanan:** Data sensitif dipastikan selalu terlihat menonjol dalam kondisi apa pun.

## 3. Implementasi Teknis — Penataan Gaya Antarmuka

Perbaikan diimplementasikan langsung pada file tata letak utama (`app.blade.php`) dengan mempertegas bobot spesifisitas menggunakan modifikasi deklarasi warna:
- Menargetkan teks umum dalam tabel *dark mode* agar berwarna putih kontras (`#F8FAFC`).
- Memaksa kolom IP Address menjadi merah pekat (`#F87171`) berserta ketebalan teks.

## 4. Struktur File yang Dibuat/Dimodifikasi

```text
SmartCampus/
└── resources/views/layouts/
    └── app.blade.php    [MODIFIED] Refactor CSS Dark Mode, penegasan warna tabel
```

---

# PROGRESS WEEK 6 — SmartCampus

**Anggota:** Teofilus Juan Puapadang (2472053)  
**Branch:** `Week-6---Teofilus-Juan-Puapadang`  
**Tanggal:** 13 Juni 2026

## 1. Gambaran Umum Fitur

Fokus pengembangan pada minggu ini adalah implementasi fitur **Undo / Redo** pada pengelolaan tugas (Assignment) untuk aktor Dosen, serta penyempurnaan sudut pandang (Point of View / POV) dan validasi formulir (Guard Clauses) dalam sistem penilaian.

## 2. Design Pattern — Command & Memento Pattern

Untuk mewujudkan fitur *Undo / Redo*, sistem memanfaatkan kombinasi dari **Command Pattern** (untuk membungkus aksi CRUD) dan prinsip **Memento Pattern** (untuk menyimpan dan mengembalikan status objek ke kondisi sebelumnya).

-   `CommandInterface`: Interface untuk semua aksi (Create, Edit, Delete).
-   `TaskCommandInvoker`: Kelas yang bertindak sebagai *Invoker* sekaligus pengelola riwayat status (Memento). Status disimpan di dalam PHP Session (`task_undo_stack`, `task_redo_stack`) yang berisi ID rekaman dan aksi yang dilakukan.
-   Tiga perintah konkrit: `CreateTaskCommand`, `EditTaskCommand`, dan `DeleteTaskCommand` yang mendefinisikan cara mengubah dan mengembalikan data tugas (*assignment*).

## 3. Penyempurnaan POV (Point of View) & Form Guard

Melakukan perbaikan logika tampilan (*View*) agar sistem lebih aman dan relevan bagi setiap aktor:

-   **Guard Clause Penilaian (Controller & UI):** Mengamankan fungsi penilaian agar dosen tidak bisa memberikan nilai melebihi batas skor maksimal (`max_score`) dari masing-masing tugas, baik pada validasi `SubmissionController` maupun proteksi input HTML di _modal_.
-   **Kustomisasi POV Status:** Memperbaiki teks keterangan status di halaman tugas. Status akan menampilkan teks "Ditutup" ketika diakses oleh Dosen/Admin (sebagai pengelola), namun tetap "Terlambat" saat dilihat oleh Mahasiswa (sebagai partisipan).
-   **Pembersihan Elemen:** Menyembunyikan opsi filter status *deadline* untuk akun Dosen agar tampilan lebih terfokus.

## 4. Struktur File yang Dibuat/Dimodifikasi

```text
SmartCampus/
├── app/
│   ├── Http/Controllers/
│   │   ├── AssignmentController.php       [MODIFIED] Integrasi Invoker, Undo/Redo logic
│   │   └── SubmissionController.php       [MODIFIED] Guard max_score dynamic
│   └── Services/Command/
│       ├── TaskCommandInvoker.php         [NEW] Invoker & Memento state manager
│       ├── CreateTaskCommand.php          [NEW] Concrete command
│       ├── EditTaskCommand.php            [NEW] Concrete command (saves before & after)
│       └── DeleteTaskCommand.php          [NEW] Concrete command (soft/hard delete handling)
│
├── resources/views/
│   └── assignments/
│       ├── index.blade.php                [MODIFIED] Tombol Undo/Redo & filter POV Dosen
│       └── show.blade.php                 [MODIFIED] POV checks & modal max_score
└── routes/
    └── web.php                            [MODIFIED] Rute POST untuk undo & redo
```
