# Laporan Progress Week 5 - Francisco Valentino

Pada minggu kelima ini, pengembangan berfokus pada **Penyempurnaan Logika Bisnis, Restrukturisasi Hak Akses (Role-Based Access Control), dan Penambahan Fitur Inti** berdasarkan *feedback* evaluasi dari dosen pembimbing.

## Daftar Perubahan dan Fitur Baru

### 1. Pembersihan UI dan Penyesuaian Sudut Pandang (POV)
- **Penghapusan Menu Dummy:** Membersihkan *sidebar* dari menu-menu yang belum berfungsi (seperti Notifikasi Mahasiswa, Export Laporan dummy, dsb.) agar UI terlihat lebih rapi dan fungsional saat presentasi.
- **Penyesuaian Label Status Deadline:** Mengubah logika tampilan status tenggat waktu tugas. Jika sebuah tugas sudah melewati *deadline*, sistem akan menampilkan status **"Terlambat"** untuk *role* Mahasiswa, namun menampilkan **"Ditutup"** untuk *role* Dosen dan Admin agar secara konteks lebih masuk akal.

### 2. Guard Validation pada Form Penilaian
- Menambahkan penjagaan (guard) dinamis pada form input nilai oleh dosen.
- Sebelumnya nilai dibatasi *hardcode* maksimal 100. Sekarang batasan input nilai (baik di atribut HTML `max` maupun validasi *backend* Laravel) menyesuaikan secara dinamis dengan nilai `max_score` yang ditetapkan dosen saat membuat tugas tersebut. (Contoh: Jika skor maksimal diset 50, maka dosen tidak akan bisa menginput nilai 51).

### 3. Restrukturisasi Hak Akses Admin (Sistem Administrator)
- **Pencabutan Akses Akademik:** Menghapus kemampuan Admin untuk melihat daftar tugas dan detail *submission* mahasiswa. Secara logika bisnis, admin sistem tidak perlu mengurusi konten mata kuliah.
- **Penambahan CRUD Manajemen Pengguna:** Menambahkan controller dan antarmuka bagi Admin untuk dapat melakukan *Create, Read, Update, Delete* pada entitas pengguna (Dosen, Mahasiswa, dan Admin lain).
- **Penambahan CRUD Mata Kuliah:** Menambahkan fungsionalitas bagi Admin untuk mengelola data Mata Kuliah (*Courses*) dan menetapkan Dosen pengampu.

### 4. Penambahan Fitur Export Nilai (Dosen)
- Mengembangkan fitur *Export to CSV* pada halaman detail tugas dosen.
- Dosen kini dapat mengunduh daftar nilai seluruh mahasiswa yang mengumpulkan tugas tersebut dalam format CSV yang kompatibel dengan Microsoft Excel untuk kebutuhan pelaporan eksternal.

### 5. Penambahan Fitur Rekap Nilai (Mahasiswa)
- Menambahkan menu **"Rekap Nilai"** (*Transcript*) pada dasbor Mahasiswa.
- Halaman ini merangkum dan mengelompokkan seluruh riwayat tugas beserta nilai akhir yang didapat oleh mahasiswa, disusun dengan rapi berdasarkan Mata Kuliah yang diambil.

---

**Status Target:** Seluruh perbaikan dan target fungsionalitas tambahan untuk Week 5 telah berhasil diimplementasikan dan di-*push* ke branch GitHub `Week-5---Francisco-Valentino`.
