# 🚀 SETUP GUIDE — SmartCampus

Panduan setup project SmartCampus untuk semua anggota kelompok.

---

## Prasyarat (Wajib Sudah Terinstall)

| Software     | Versi Minimum | Cek Versi                       |
| ------------ | ------------- | ------------------------------- |
| **PHP**      | 8.2+          | `php -v`                        |
| **Composer** | 2.x           | `composer -V`                   |
| **MySQL**    | 5.7+          | Via XAMPP / MySQL Workbench     |
| **Git**      | 2.x           | `git -v`                        |
| **XAMPP**    | Terbaru       | Pastikan Apache & MySQL running |

> ⚠️ **Pastikan XAMPP sudah distart** (Apache & MySQL harus hijau) sebelum memulai.

---

## Langkah 1 — Clone / Pull Repository

Jika **belum punya** project-nya:

```bash
git clone <URL_REPOSITORY>
cd Smart-Campus_Tugas-Besar-PDPL

```

Jika **sudah punya** (tinggal pull update terbaru):

```bash
git pull origin main

```

Pindah ke branch masing-masing jika perlu:

```bash
git checkout <nama-branch-kamu>

```

---

## Langkah 2 — Install Dependencies

Buka terminal, masuk ke folder `SmartCampus/`, lalu jalankan:

```bash
cd SmartCampus
composer install

```

Tunggu sampai selesai (butuh internet).

---

## Langkah 3 — Konfigurasi Environment (.env)

Copy file `.env.example` menjadi `.env`:

**Windows (CMD):**

```bash
copy .env.example .env

```

**Windows (PowerShell):**

```powershell
Copy-Item .env.example .env

```

Lalu buka file `.env` dan pastikan bagian database seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartcampus
DB_USERNAME=root
DB_PASSWORD=

```

> Jika MySQL kamu pakai password, isi `DB_PASSWORD=passwordkamu`

---

## Langkah 4 — Generate App Key

```bash
php artisan key:generate

```

---

## Langkah 5 — Buat Database

Buka **MySQL Workbench** atau **phpMyAdmin**, lalu jalankan:

```sql
CREATE DATABASE IF NOT EXISTS smartcampus
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

```

Atau lewat **terminal**:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS smartcampus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

```

---

## Langkah 6 — Jalankan Migration + Seeder

```bash
php artisan migrate:fresh --seed

```

Perintah ini akan:

-   Membuat semua tabel (14 tabel) secara otomatis.
-   Mengisi data dummy (13 akun: 1 Admin, 7 Dosen, 5 Mahasiswa).
-   Mengisi data Mata Kuliah, Tugas (Assignments) dengan variasi deadline, dan data pengumpulan tugas (Submissions).

---

## Langkah 7 — Jalankan Server

Buka terminal pertama untuk menyalakan server web:

```bash
php artisan serve

```

Buka browser dan akses: **http://127.0.0.1:8000**

---

## Langkah 8 — Login & Test

Halaman login akan muncul. Gunakan akun berikut untuk testing:

### 🔧 Admin

| Email                     | Password   |
| ------------------------- | ---------- |
| `admin@smartcampus.ac.id` | `password` |

### 👨‍🏫 Dosen

| Email                                                   | Password   | Mata Kuliah                 |
| ------------------------------------------------------- | ---------- | --------------------------- |
| `budi@smartcampus.ac.id`                                | `password` | Pola Desain Perangkat Lunak |
| `sari@smartcampus.ac.id`                                | `password` | Web Dasar                   |
| `ahmad@smartcampus.ac.id`                               | `password` | Pancasila                   |
| `maya@smartcampus.ac.id`                                | `password` | Proyek PL (Testing H-1)     |
| _(Tersedia juga akun: dewi@..., rizki@..., hendra@...)_ |            |                             |

### 👨‍🎓 Mahasiswa

| Email                     | Password   |
| ------------------------- | ---------- |
| `francisco@student.ac.id` | `password` |
| `juan@student.ac.id`      | `password` |
| `calvin@student.ac.id`    | `password` |
| `dave@student.ac.id`      | `password` |
| `andi@student.ac.id`      | `password` |

---

## Langkah 9 — Testing Fitur Background (Scheduler)

Untuk mengetes fitur **Sistem Deadline Reminder Otomatis** (Observer Pattern) yang berjalan di latar belakang:

1. Buka **terminal baru** (biarkan `php artisan serve` tetap menyala di terminal pertama).
2. Pastikan posisi _path_ berada di dalam folder `SmartCampus/`.
3. Jalankan perintah manual ini:

```bash
php artisan reminder:send-deadline

```

4. Cek terminal, akan muncul log bahwa tugas H-1 terdeteksi dan notifikasi telah dikirim.
5. Login ke web menggunakan akun `dave@student.ac.id` untuk melihat badge notifikasi di dashboard.

_(Catatan: Di server production, perintah ini akan dijalankan otomatis setiap jam 08:00 pagi oleh cron job `php artisan schedule:work`)_.

---

## Ringkasan Perintah (Quick Start)

```bash
cd SmartCampus
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve

```

Lalu buka **http://127.0.0.1:8000** di browser.

---

## Troubleshooting

### ❌ "Could not open input file: artisan"

**Solusi:** Kamu belum masuk ke folder `SmartCampus/`. Jalankan `cd SmartCampus` dulu.

### ❌ "SQLSTATE[HY000] [1049] Unknown database 'smartcampus'"

**Solusi:** Database belum dibuat. Jalankan `CREATE DATABASE smartcampus;` di MySQL Workbench.

### ❌ "SQLSTATE[HY000] [2002] Connection refused"

**Solusi:** MySQL belum running. Buka XAMPP dan start MySQL.

### ❌ "No application encryption key has been specified"

**Solusi:** Jalankan `php artisan key:generate`.

### ❌ "composer install" error / timeout

**Solusi:** Pastikan internet stabil. Coba `composer install --no-dev` jika lambat.

---

## Pembagian Tugas

| Anggota                 | Fitur                                                               |
| ----------------------- | ------------------------------------------------------------------- |
| **Francisco Valentino** | 1. Login & Role Management, 7. Activity Log, 12. OTP                |
| **Dave Andrew**         | 3. Deadline Reminder, 5. Penilaian, 11. Export PDF/CSV              |
| **Teofilus Juan**       | 2. Manajemen Tugas CRUD, 8. Undo/Redo, 10. Notifikasi Multi-Channel |
| **Calvin Yohanis**      | 4. Tracking Progress, 6. Penyimpanan Data, 9. Mode Tampilan         |
