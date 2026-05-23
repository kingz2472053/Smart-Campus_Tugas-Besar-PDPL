# PROGRESS WEEK 2 — SmartCampus

**Mata Kuliah:** Pola Desain Perangkat Lunak  
**Anggota:** Dave Andrew (2172015)  
**Branch:** `week-2---Dave-Andrew`  
**Tanggal:** 15 Mei 2026

---

## 1. Gambaran Umum Fitur

Fitur **Sistem Deadline Reminder Otomatis** berfungsi untuk memberikan pengingat secara proaktif kepada mahasiswa yang memiliki tugas dengan batas waktu (deadline) H-1. Sistem ini bekerja di latar belakang (background process) untuk menyaring mahasiswa yang belum menyelesaikan tugas dan mengirimkan notifikasi.

### Apa yang dilakukan:

-   **Pencarian Otomatis:** Mencari tugas yang memiliki deadline tepat besok hari.
-   **Filtering Cerdas:** Hanya mengirimkan pengingat kepada mahasiswa yang status pengerjaannya belum `completed`.
-   **Integrasi Observer:** Menggunakan pola desain Observer untuk memisahkan logika pencarian tugas dengan logika pengiriman notifikasi.

---

## 2. Design Pattern — Observer Pattern (Fitur 3: Deadline Reminder)

Mengimplementasikan **Observer Pattern** secara murni (OOP) untuk menangani trigger notifikasi saat deadline mendekat.

### Mengapa Observer Pattern?

-   **Loose Coupling:** Model `Assignment` (Subject) tidak perlu tahu detail bagaimana notifikasi dikirim (Email/Dashboard). Ia hanya perlu memberitahu para "pengamat" (Observers) bahwa ada event deadline.
-   **Extensibility:** Memudahkan penambahan jenis notifikasi baru di masa depan tanpa mengubah kode inti pencarian tugas.

### Struktur Kelas:

```

SubjectInterface (Interface)
│
└── Assignment (Concrete Subject / Model)
└── Menyimpan daftar Observers & memicu notifyObservers()

ObserverInterface (Interface)
│
└── DeadlineNotifier (Concrete Observer)
└── Menerima data tugas & target mahasiswa, lalu membuat record Notification

```

### File yang dibuat:

-   `app/Contracts/SubjectInterface.php` — Kontrak untuk objek yang diamati.
-   `app/Contracts/ObserverInterface.php` — Kontrak untuk objek yang mengamati.
-   `app/Observers/DeadlineNotifier.php` — Implementasi pengamat yang mengeksekusi pembuatan notifikasi ke DB.
-   `app/Models/Assignment.php` — Dimodifikasi untuk mengimplementasikan `SubjectInterface`.

---

## 3. Implementasi Teknis — Artisan Command & Scheduler

Fitur ini membutuhkan otomatisasi tanpa intervensi manual dari pengguna.

### Artisan Command:

Dibuat perintah kustom `php artisan reminder:send-deadline` yang melakukan:

1. Query tugas dengan deadline besok menggunakan `Carbon::tomorrow()`.
2. Filter mahasiswa yang terdaftar (`enrollments`) namun belum mengumpulkan (`submissions.progress != completed`).
3. Memicu `notifyObservers()` pada setiap objek tugas yang ditemukan.

### Scheduler (Penjadwalan):

Didaftarkan pada `routes/console.php` untuk berjalan otomatis setiap hari pada jam 08:00 pagi:

```php
Schedule::command('reminder:send-deadline')->dailyAt('08:00');

```

---

## 4. Integrasi Dashboard & Notifikasi

Agar mahasiswa dapat melihat pengingat tersebut, dilakukan integrasi pada sisi tampilan:

-   **DashboardController:** Dimodifikasi pada method `studentDashboard()` untuk mengambil 5 notifikasi terbaru dari database.
-   **Blade Template:** Menambahkan komponen "Notifikasi Terbaru" pada `resources/views/dashboard/student.blade.php` dengan badge status "Baru".
-   **Seeder:** Memperbarui `SmartCampusSeeder.php` dengan "Tugas ke-9" yang diset deadline H-1 untuk keperluan verifikasi fitur.

---

## 5. Struktur File yang Dibuat/Dimodifikasi

```
SmartCampus/
├── app/
│   ├── Contracts/
│   │   ├── ObserverInterface.php        [NEW] Interface Observer
│   │   └── SubjectInterface.php         [NEW] Interface Subject
│   │
│   ├── Console/
│   │   └── Commands/
│   │       └── SendDeadlineReminders.php [NEW] Logic pencari deadline H-1
│   │
│   ├── Http/Controllers/
│   │   └── DashboardController.php      [MODIFIED] Integration with Notifications
│   │
│   ├── Models/
│   │   └── Assignment.php               [MODIFIED] Subject implementation
│   │
│   └── Observers/
│       └── DeadlineNotifier.php         [NEW] Notification trigger logic
│
├── database/seeders/
│   └── SmartCampusSeeder.php            [MODIFIED] Added H-1 deadline test data
│
├── resources/views/dashboard/
│   └── student.blade.php                [MODIFIED] Added Notification UI component
│
└── routes/
    └── console.php                      [MODIFIED] Registered daily task scheduler

```

---

## 6. Testing & Verifikasi

-   ✅ `php artisan migrate:fresh --seed` — Database bersih dengan data uji H-1 tersedia.
-   ✅ `php artisan reminder:send-deadline` — Command berhasil mendeteksi tugas H-1 dan memicu Observer.
-   ✅ **Verifikasi Dashboard:** Login sebagai `dave@student.ac.id`, notifikasi "PENGINGAT: Tugas... akan ditutup besok!" muncul di dashboard dengan label merah.
-   ✅ **Filtering Test:** Mahasiswa yang sudah mengumpulkan tugas (progress: completed) terbukti tidak menerima notifikasi ganda.
-   ✅ **Scheduler Test:** Perintah terdaftar di `php artisan schedule:list` dengan jadwal harian 08:00.

---

## 7. Ringkasan Design Pattern yang Digunakan

| Design Pattern        | Komponen                         | Fungsi                                                       |
| --------------------- | -------------------------------- | ------------------------------------------------------------ |
| **Observer Pattern**  | `Assignment`, `DeadlineNotifier` | Memisahkan trigger waktu dengan aksi pengiriman notifikasi.  |
| **Singleton Pattern** | `ActivityLogger`                 | Digunakan secara internal untuk mencatat eksekusi scheduler. |
| **Abstract Factory**  | `UserFactoryManager`             | Digunakan dalam seeder untuk pembuatan akun mahasiswa uji.   |

---

## TODO — Week 3

-   [ ] Sinkronisasi dengan NotifFactory (Multi-Channel).
-   [ ] Implementasi pengingat via Email (SMTP).
-   [ ] Fitur "Mark as Read" pada list notifikasi dashboard.
