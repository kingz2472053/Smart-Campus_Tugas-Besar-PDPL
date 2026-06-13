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
