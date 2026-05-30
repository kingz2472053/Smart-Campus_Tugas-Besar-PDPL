# 🎤 Alur Presentasi Progres SmartCampus (Week 3)

Dokumen ini berisi panduan alur demonstrasi aplikasi SmartCampus saat presentasi di depan dosen, agar berjalan mulus dan sistematis.

---

## 1. Pembukaan (1-2 Menit)
- **Perkenalan Tim** dan pembagian tugas.
- **Konsep Aplikasi:** Jelaskan singkat bahwa SmartCampus dibangun dengan menerapkan 12 Design Pattern untuk menyelesaikan berbagai permasalahan arsitektur perangkat lunak.

---

## 2. Demo Keamanan & Audit (Fokus Francisco)

### A. Fitur 12: Autentikasi Bertingkat / OTP (Decorator Pattern)
**Skenario:** Mendemonstrasikan akun yang membutuhkan keamanan ekstra.
1. Buka halaman Login.
2. Login menggunakan akun **Admin**:
   - Email: `smartcampus.pdpl+admin@gmail.com`
   - Password: `password`
3. Tunjukkan bahwa sistem meminta **Kode OTP** (karena `otp_enabled` = true).
4. **Buka kotak masuk Gmail** (`smartcampus.pdpl@gmail.com`) di tab baru untuk menunjukkan bahwa email OTP benar-benar masuk.
5. Masukkan kode OTP dan berhasil masuk ke Dashboard Admin.
6. *Penjelasan Singkat:* "Fitur OTP ini menggunakan **Decorator Pattern**, di mana verifikasi OTP 'membungkus' proses login dasar tanpa mengubah kode utama login."

### B. Fitur 7: Riwayat Aktivitas / Activity Log (Singleton Pattern)
**Skenario:** Melihat log aktivitas seluruh pengguna.
1. Dari Dashboard Admin, buka menu **Riwayat Aktivitas**.
2. Tunjukkan daftar log (termasuk log login Admin yang baru saja dilakukan).
3. Tunjukkan fitur filter dan klik salah satu detail log.
4. *Penjelasan Singkat:* "Pencatatan ini menggunakan **Singleton Pattern** agar hanya ada satu *instance* `ActivityLogger` yang mencatat ke database secara konsisten di seluruh sistem."

---

## 3. Demo Manajemen Tugas & Penilaian (Fokus Juan & Ko Dev)

### A. Login Tanpa OTP (Abstract Factory Pattern)
1. **Logout** dari akun Admin.
2. Login menggunakan akun **Dosen** (Misal: Dr. Budi - `budi@smartcampus.ac.id` / `password`).
3. Tunjukkan bahwa dosen **langsung masuk ke dashboard** tanpa OTP (karena `otp_enabled` = false).
4. *Penjelasan Singkat:* "Pembuatan *session* dan perbedaan alur dashboard ini diatur oleh **Abstract Factory Pattern**, yang membuat *instance* pengguna berdasarkan rolenya (Admin, Dosen, Mahasiswa)."

### B. Fitur 2: Manajemen Tugas (Command Pattern)
1. Buka menu **Tugas**.
2. Tunjukkan fitur Create, Update, atau Delete tugas.
3. *Penjelasan Singkat:* "Operasi CRUD ini dibungkus menggunakan **Command Pattern**, sehingga setiap aksi (buat/edit/hapus) dienkapsulasi menjadi objek perintah yang terpisah."

### C. Fitur 5: Sistem Penilaian Otomatis (Strategy Pattern)
1. Buka detail salah satu tugas yang sudah dikumpulkan mahasiswa.
2. Berikan nilai pada tugas tersebut.
3. *Penjelasan Singkat:* "Penilaian ini menggunakan **Strategy Pattern**, di mana dosen dapat menggunakan strategi penilaian yang berbeda (Angka, Huruf, atau Lulus/Tidak Lulus) secara dinamis."

---

## 4. Demo Mahasiswa & Notifikasi (Fokus Ko Calvin & Juan)

### A. Fitur 4: Tracking Progress (State Pattern)
1. **Logout** dari akun Dosen.
2. Login menggunakan akun **Mahasiswa** (Misal: Juan - `juan@student.ac.id` / `password`).
3. Buka detail tugas yang tadi dinilai oleh dosen.
4. Tunjukkan perubahan status (*State*) tugas dari 'Submitted' menjadi 'Graded'.
5. *Penjelasan Singkat:* "Perubahan status tugas menggunakan **State Pattern**, di mana perilaku sistem berubah berdasarkan *state* pengumpulan saat ini (Draft -> Submitted -> Graded)."

### B. Fitur 10: Notifikasi MultiChannel (Factory Method Pattern)
1. Buka menu/icon lonceng **Notifikasi**.
2. Tunjukkan adanya notifikasi tugas baru atau nilai yang masuk.
3. *Penjelasan Singkat:* "Notifikasi ini di-generate menggunakan **Factory Method Pattern**, yang memungkinkan pengiriman notifikasi ke berbagai *channel* (Dashboard & Email) tanpa merombak logika utama pemanggil notifikasi."