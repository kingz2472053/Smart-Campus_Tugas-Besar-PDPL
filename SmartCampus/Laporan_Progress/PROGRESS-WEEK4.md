# PROGRESS WEEK 4 — SmartCampus
**Mata Kuliah:** Pola Desain Perangkat Lunak  
**Anggota:** Calvin Yohanis (2272017)  
**Branch:** `Week-4---Calvin-Yohanis`  
**Tanggal:** 30 Mei 2026

---

## 1. Gambaran Umum Fitur

Fitur **Manajemen Eksperimen Gabungan Tugas (Assignment)** berfungsi untuk memfasilitasi dosen dalam membuat, membaca, serta memperbarui (*CRUD*) data penugasan kuliah secara aman. Fitur ini dirancang untuk menangani integrasi tingkat tinggi antar komponen arsitektur tim agar tidak terjadi kegagalan data saat sistem melakukan manipulasi ke database.

### Apa yang dilakukan:

-   **Abstraksi Data Terpusat:** Mengisolasi query database dari controller utama agar perubahan skema database tidak merusak fungsionalitas sistem.
-   **Sinkronisasi Otomatis:** Mengamankan parameter krusial seperti penanganan waktu (`deadline`) dan pencatatan dosen pengampu (`created_by`) saat proses penyimpanan data berlangsung.
-   **Harmonisasi Multi-Pattern:** Menghubungkan proses penyimpanan data tugas dengan sistem tindakan lanjutan (*Command Pattern*) milik anggota tim lain secara bersamaan.

---

## 2. Design Pattern — Repository Pattern (Fitur Kelola Tugas)

Mengimplementasikan **Repository Pattern** untuk memisahkan logika bisnis aplikasi (*Controller*) dengan logika akses penyimpanan data (*Data Access Layer*).

### Mengapa Repository Pattern?

-   **Loose Coupling & Clean Code:** `AssignmentController` tidak lagi berkomunikasi langsung dengan Model Eloquent. Controller hanya tahu cara memanggil metode fungsi lewat kontrak perantara.
-   **Maintainability:** Jika di masa mendatang struktur database berubah (seperti perubahan nama kolom dari `due_date` menjadi `deadline`), tim hanya perlu merubah kode di file Repository tanpa menyentuh file Controller sama sekali.

### Struktur Kelas:

AssignmentRepositoryInterface (Interface Kontrak)│└── AssignmentRepository (Concrete Repository)└── Mengimplementasikan logika Eloquent (Assignment::create / update)
### File yang dibuat:

-   `app/Repositories/Contracts/AssignmentRepositoryInterface.php` — Kontrak fungsi CRUD utama untuk objek tugas.
-   `app/Repositories/Eloquent/AssignmentRepository.php` — Implementasi konkret yang menangani manipulasi ke database SQLite/MySQL.

---

## 3. Implementasi Teknis — Dependency Injection & Pattern Collaboration

Fitur ini memanfaatkan fitur kontainer Laravel untuk menyuntikkan (*inject*) kelas repository secara otomatis ke dalam Controller.

### Dependency Injection via Constructor:

Pada file `AssignmentController.php`, objek repository dimasukkan melalui fungsi `__construct` menggunakan interface kontraknya:

```php
public function __construct(AssignmentRepositoryInterface $assignmentRepo) {
    $this->invoker = new TaskCommandInvoker();
    $this->assignmentRepo = $assignmentRepo;
}
Kolaborasi Dua Pola Desain (Repository & Command):Di dalam method store(), sistem mengeksekusi dua pola desain berbeda sekaligus untuk menangani satu alur pembuatan tugas:PHP// 1. Eksekusi Repository Pattern untuk keamanan database
$this->assignmentRepo->store($validated); 

$assignment = $this->invoker->execute($command, Auth::id());
4. Integrasi Dashboard & Tampilan Web


Perbaikan arsitektur ini berdampak langsung pada fungsionalitas visual halaman web:Form Edit & Create Aktual: Penggantian penanganan data ke kolom deadline membuat form pembuatan dan pembaruan tugas kini berjalan mulus tanpa memicu error sistem.Widget Detail Tugas Aktif: Menampilkan penghitungan mundur sisa waktu secara dinamis ("Mendekati Deadline") serta nama dosen pembuat tugas secara presisi pada halaman detail.Sinkronisasi Submissions: Menghubungkan identitas tugas secara aman sehingga tabel pengumpulan (submission) milik mahasiswa dapat tampil langsung di bawah deskripsi tugas.
5. Struktur File yang Dibuat/DimodifikasiSmartCampus/
├── app/
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   └── AssignmentRepositoryInterface.php [NEW] Interface kontrak CRUD tugas
│   │   └── Eloquent/
│   │       └── AssignmentRepository.php          [NEW] Implementasi query Eloquent
│   │
│   ├── Http/Controllers/
│   │   └── AssignmentController.php             [MODIFIED] Injection repository & store logic
│   │
│   └── Providers/
│       └── AppServiceProvider.php               [MODIFIED] Binding Interface ke Concrete Repository
│
├── database/
│   └── database.sqlite                          [MODIFIED] Sinkronisasi skema data tugas baru
│
└── resources/views/dosen/assignments/
    └── detail.blade.php                         [MODIFIED] Integrasi data deadline & nama dosen
6. Testing & Verifikasi✅ Verifikasi Pembuatan Tugas: Pengujian form pembuatan tugas baru berhasil 100% dan memicu alert sukses berwarna hijau: "Tugas berhasil dibuat."✅ Verifikasi Pembaruan Tugas: Aksi mengedit tugas berjalan lancar tanpa memicu error database Integrity Constraint Violation.✅ Verifikasi Relasi Data: Akun mahasiswa (calvin@student.ac.id) terbukti sukses mengumpulkan file tugas ke dalam sistem penugasan yang baru saja di-abstraksi oleh Repository.✅ GitHub Sync: Berhasil menyelesaikan merge conflict pada file eksternal, membuat branch lokal baru, dan melakukan git push ke branch Week-4---Calvin-Yohanis dengan status bersih (clean).
7. Ringkasan Design Pattern yang DigunakanDesign PatternKomponenFungsiRepository PatternAssignmentRepositoryInterface, AssignmentRepositoryMengisolasi query database dan memisahkan tanggung jawab Controller.Command PatternTaskCommandInvoker, CreateTaskCommandMembungkus perintah operasi pembuatan tugas untuk pencatatan riwayat sistem.