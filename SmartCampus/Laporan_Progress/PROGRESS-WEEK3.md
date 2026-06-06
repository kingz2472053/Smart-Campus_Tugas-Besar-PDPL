# 📈 Laporan Progres Mingguan - SmartCampus

## Detail Laporan
- **Minggu ke-** : 3 (Tiga)
- **Fokus Pengerjaan** : Penyelesaian Fitur Francisco (Activity Log, Real Email OTP, Role Management), Dokumentasi Design Pattern, & Master To-Do List.
- **Tanggal Update** : 23 Mei 2026

---

## 🎯 Pencapaian Minggu 3 (Selesai 100% Bagian Francisco)

Pada minggu ketiga ini, fokus pengerjaan berada pada penyelesaian akhir fitur-fitur yang menjadi tanggung jawab **Francisco Valentino** (Fitur 1, Fitur 7, dan Fitur 12), serta mempersiapkan senjata presentasi berupa dokumentasi arsitektur perangkat lunak yang matang untuk menghadapi pertanyaan Dosen PDPL.

### 1. Master To-Do List & Dokumentasi Arsitektur
- ✅ Membuat file **`TODO-LIST-PROJECT.md`** sebagai panduan kerja seluruh kelompok dari awal fase hingga fase akhir integrasi.
- ✅ Memperbarui file **`README.md`** dengan penjelasan mendalam mengenai 12 Design Pattern yang digunakan, mencakup alasan teknis (seperti *Open/Closed Principle*, efisiensi memori, skalabilitas) untuk persiapan Q&A dengan dosen.

### 2. Fitur 7: Riwayat Aktivitas (Activity Log - Singleton Pattern)
- ✅ Mengembangkan `ActivityLogController` yang mampu memfilter log aktivitas berdasarkan role (Admin dapat melihat semua log, Dosen/Mahasiswa hanya melihat log miliknya sendiri).
- ✅ Mengimplementasikan UI/UX lengkap pada `resources/views/activity-logs/index.blade.php` dengan badge interaktif, form filter, dan modal JSON.
- ✅ Menambahkan `show.blade.php` untuk melihat detail lengkap satu log aktivitas khusus Admin.
- ✅ Melakukan perbaikan pada routing `web.php` dan `layouts/app.blade.php` sehingga navigasi *sidebar* berfungsi normal tanpa link yang terputus.

### 3. Fitur 12: Autentikasi Bertingkat OTP (Decorator Pattern)
- ✅ Membuat file *migration* baru untuk menambahkan kolom `otp_enabled` pada tabel `users`.
- ✅ Mengubah logika `OTPDecorator` agar mengecek status `otp_enabled` di database, sehingga aktivasi OTP menjadi dinamis (tidak di-hardcode).
- ✅ **Implementasi Real Email (Laravel Mail):** Mengubah sistem pengiriman OTP yang awalnya hanya tampil di layar menjadi pengiriman email sungguhan (*production-ready*) menggunakan Gmail SMTP (`smartcampus.pdpl@gmail.com`).
- ✅ Menerapkan trik *Gmail Aliases* (`smartcampus.pdpl+admin@gmail.com`) pada `SmartCampusSeeder` untuk mempermudah demonstrasi fitur email tanpa memerlukan banyak akun percobaan.
- ✅ Menghapus fitur kode *[DEV MODE]* dari layar verifikasi untuk simulasi yang realistis.

### 4. Fitur 1: Login & Role Management (Abstract Factory)
- ✅ Sinkronisasi ulang data di `SmartCampusSeeder` (Courses, Enrollments, Assignments, Submissions) agar saling terhubung tanpa *crash*.
- ✅ Memastikan `RoleMiddleware` dan routing bekerja harmonis membatasi akses URL berdasarkan *role* yang sedang aktif.

---

## 🎯 Pencapaian Minggu 3 (Bagian Dave & Andrew)

### 1. Fitur 5: Penilaian Otomatis (Strategy Pattern)
- ✅ Mengimplementasikan **Strategy Pattern** untuk menangani berbagai algoritma penilaian yang mungkin berbeda-beda untuk setiap jenis tugas.
- ✅ Dosen dapat memilih strategi penilaian (misal: `NumericGradingStrategy`, `LetterGradingStrategy`, `PredicateGradingStrategy`) tanpa mengubah kode controller (*Open/Closed Principle*).
- ✅ Dibuat Service `GradingService` yang memanfaatkan `GradingContext` untuk mengeksekusi strategi penilaian berdasarkan konfigurasi `Assignment`.

### 2. Fitur 10: Notifikasi Multi-Channel (Factory Method)
- ✅ Mengimplementasikan **Factory Method Pattern** untuk menangani pembuatan objek notifikasi berdasarkan channel yang dipilih.
- ✅ Memungkinkan sistem mengirimkan notifikasi melalui berbagai kanal (Database, Email).
- ✅ Sistem diperluas untuk mendukung pengiriman notifikasi via email (SMTP) menggunakan `EmailNotifier` yang diinstansiasi melalui `NotificationFactory` (*Decoupling* dan *Scalability*).

### Ringkasan Design Pattern yang Digunakan (Dave & Andrew)
| Design Pattern       | Komponen              | Fungsi                                      |
| -------------------- | --------------------- | ------------------------------------------- |
| **Strategy Pattern** | `GradingService`      | Memilih algoritma penilaian saat runtime.   |
| **Factory Method**   | `NotificationFactory` | Membuat objek notifikasi berdasarkan kanal. |
